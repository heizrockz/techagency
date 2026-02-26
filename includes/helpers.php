<?php
/**
 * Mico Sage — Utility helpers
 */
require_once __DIR__ . '/db.php';

/* ── i18n ────────────────────────────────────────────────── */
function getCurrentLocale(): string {
    return $_SESSION['locale'] ?? DEFAULT_LOCALE;
}

function appSetLocale(string $locale): void {
    if (in_array($locale, SUPPORTED_LOCALES)) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['locale'] = $locale;
    }
}

function isRTL(): bool {
    return getCurrentLocale() === 'ar';
}

function t(string $key): string {
    static $translations = [];
    $locale = getCurrentLocale();
    if (!isset($translations[$locale])) {
        $file = __DIR__ . '/../lang/' . $locale . '.php';
        $translations[$locale] = file_exists($file) ? require $file : [];
    }
    // Also check DB translations
    if (!isset($translations[$locale][$key])) {
        try {
            $db = getDB();
            $stmt = $db->prepare('SELECT trans_value FROM translations WHERE trans_key = ? AND locale = ? LIMIT 1');
            $stmt->execute([$key, $locale]);
            $row = $stmt->fetch();
            if ($row) {
                $translations[$locale][$key] = $row['trans_value'];
            }
        } catch (Exception $e) {}
    }
    return $translations[$locale][$key] ?? $key;
}

/* ── Dynamic content from DB ──────────────────────────────── */
function getContent(string $sectionKey, ?string $locale = null): string {
    $locale = $locale ?? getCurrentLocale();
    try {
        $db = getDB();
        $stmt = $db->prepare('SELECT value FROM contents WHERE section_key = ? AND locale = ? LIMIT 1');
        $stmt->execute([$sectionKey, $locale]);
        $row = $stmt->fetch();
        return $row ? $row['value'] : t($sectionKey);
    } catch (Exception $e) {
        return t($sectionKey);
    }
}

/* ── Site Settings ─────────────────────────────────────────── */
function getSetting(string $key, string $default = ''): string {
    static $cache = null;
    if ($cache === null) {
        try {
            $db = getDB();
            $rows = $db->query('SELECT setting_key, setting_value FROM site_settings')->fetchAll();
            $cache = [];
            foreach ($rows as $r) $cache[$r['setting_key']] = $r['setting_value'];
        } catch (Exception $e) {
            $cache = [];
        }
    }
    return $cache[$key] ?? $default;
}

function getLocaleSetting(string $baseKey): string {
    return getSetting($baseKey . '_' . getCurrentLocale(), getSetting($baseKey . '_en', ''));
}

/* ── SEO meta from DB ──────────────────────────────────────── */
function getSeoMeta(string $page, ?string $locale = null): array {
    $locale = $locale ?? getCurrentLocale();
    $defaults = ['title' => APP_NAME, 'description' => '', 'keywords' => ''];
    try {
        $db = getDB();
        $stmt = $db->prepare('SELECT title, description, keywords FROM seo_meta WHERE page = ? AND locale = ? LIMIT 1');
        $stmt->execute([$page, $locale]);
        $row = $stmt->fetch();
        return $row ? array_merge($defaults, $row) : $defaults;
    } catch (Exception $e) {
        return $defaults;
    }
}

/* ── Dynamic services from DB ─────────────────────────────── */
function getServices(): array {
    try {
        $db = getDB();
        $locale = getCurrentLocale();
        $stmt = $db->prepare('SELECT s.*, st.title, st.description
            FROM services s
            LEFT JOIN service_translations st ON s.id = st.service_id AND st.locale = ?
            WHERE s.is_active = 1
            ORDER BY s.sort_order');
        $stmt->execute([$locale]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/* ── Dynamic products from DB ─────────────────────────────── */
function getProducts(?string $category = null): array {
    try {
        $db = getDB();
        $locale = getCurrentLocale();
        if ($category) {
            $stmt = $db->prepare('SELECT p.*, pt.title, pt.description
                FROM products p
                LEFT JOIN product_translations pt ON p.id = pt.product_id AND pt.locale = ?
                WHERE p.is_active = 1 AND p.category = ?
                ORDER BY p.sort_order');
            $stmt->execute([$locale, $category]);
        } else {
            $stmt = $db->prepare('SELECT p.*, pt.title, pt.description
                FROM products p
                LEFT JOIN product_translations pt ON p.id = pt.product_id AND pt.locale = ?
                WHERE p.is_active = 1
                ORDER BY p.sort_order');
            $stmt->execute([$locale]);
        }
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/* ── Clients from DB ──────────────────────────────────────── */
function getClients(): array {
    try {
        $db = getDB();
        $stmt = $db->query('SELECT * FROM clients WHERE is_active = 1 ORDER BY sort_order');
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/* ── Dynamic booking fields from DB ───────────────────────── */
function getBookingFields(): array {
    try {
        $db = getDB();
        $locale = getCurrentLocale();
        $stmt = $db->prepare('SELECT bf.*, bft.label, bft.placeholder
            FROM booking_fields bf
            LEFT JOIN booking_field_translations bft ON bf.id = bft.field_id AND bft.locale = ?
            WHERE bf.is_active = 1
            ORDER BY bf.sort_order');
        $stmt->execute([$locale]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/* ── CSRF ──────────────────────────────────────────────────── */
function generateCsrfToken(): string {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(string $token): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/* ── Misc ──────────────────────────────────────────────────── */
function e(?string $str): string {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function baseUrl(string $path = ''): string {
    $base = rtrim(BASE_URL, '/');
    if ($path === '' || $path === '/') {
        return $base . '/';
    }
    return $base . '/' . ltrim($path, '/');
}

/**
 * Returns current URL with a modified lang parameter
 */
function getCurrentUrlWithLang(string $lang): string {
    $uri = $_SERVER['REQUEST_URI'];
    $parts = parse_url($uri);
    $query = [];
    if (isset($parts['query'])) {
        parse_str($parts['query'], $query);
    }
    $query['lang'] = $lang;
    $newQuery = http_build_query($query);
    return $parts['path'] . '?' . $newQuery;
}

if (!function_exists('getColorRgb')) {
    function getColorRgb(string $color = 'cobalt'): string {
        $colors = [
            'cobalt'  => '59, 130, 246',
            'violet'  => '139, 92, 246',
            'emerald' => '16, 185, 129',
            'cyan'    => '6, 182, 212',
            'pink'    => '236, 72, 153',
            'orange'  => '249, 115, 22',
        ];
        return $colors[$color] ?? $colors['cobalt'];
    }
}



/* ── Portfolio projects from DB ────────────────────────────── */
function getPortfolioProjects(?string $category = null): array {
    try {
        $db = getDB();
        $locale = getCurrentLocale();
        $sql = 'SELECT p.*, pt.title, pt.description, pt.client_name, pt.tags
                FROM portfolio_projects p
                LEFT JOIN portfolio_project_translations pt ON p.id = pt.project_id AND pt.locale = ?
                WHERE p.is_active = 1';
        $params = [$locale];
        if ($category && $category !== 'all') {
            $sql .= ' AND p.category = ?';
            $params[] = $category;
        }
        $sql .= ' ORDER BY p.is_featured DESC, p.sort_order ASC';
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/* ── Blogs from DB ────────────────────────────── */
function getBlogs(): array {
    try {
        $db = getDB();
        $locale = getCurrentLocale();
        // Check if view_count column exists to avoid crash before migration
        $viewCountPart = '';
        try {
            $db->query("SELECT view_count FROM blogs LIMIT 1");
            $viewCountPart = ", b.view_count";
        } catch (Exception $e) {}

        $sql = "SELECT b.*, bt.title, bt.description $viewCountPart
                FROM blogs b
                LEFT JOIN blog_translations bt ON b.id = bt.blog_id AND bt.locale = ?
                WHERE b.is_active = 1
                ORDER BY b.sort_order ASC, b.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$locale]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

function getBlogBySlug(string $slug): ?array {
    try {
        $db = getDB();
        $locale = getCurrentLocale();
        // Check if view_count column exists
        $viewCountPart = '';
        try {
            $db->query("SELECT view_count FROM blogs LIMIT 1");
            $viewCountPart = ", b.view_count";
        } catch (Exception $e) {}

        $sql = "SELECT b.*, bt.title, bt.description, bt.content $viewCountPart
                FROM blogs b
                LEFT JOIN blog_translations bt ON b.id = bt.blog_id AND bt.locale = ?
                WHERE b.slug = ? AND b.is_active = 1
                LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$locale, $slug]);
        return $stmt->fetch() ?: null;
    } catch (Exception $e) {
        return null;
    }
}

/* ── SVG Icon helper (inline SVGs for service/product cards) ── */
function getIconSvg(string $icon, string $color = 'cobalt'): string {
    $colors = [
        'cobalt'  => '#3b82f6',
        'violet'  => '#8b5cf6',
        'emerald' => '#10b981',
        'cyan'    => '#06b6d4',
        'pink'    => '#ec4899',
        'orange'  => '#f97316',
    ];
    $c = $colors[$color] ?? '#3b82f6';

    $icons = [
        'code' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="'.$c.'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>',
        'monitor' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="'.$c.'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
        'chart' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="'.$c.'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>',
        'car' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="'.$c.'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 16H9m10 0h3v-3.15a1 1 0 0 0-.84-.99L16 11l-2.7-3.6a1 1 0 0 0-.8-.4H5.24a2 2 0 0 0-1.8 1.1l-.8 1.63A6 6 0 0 0 2 12.42V16h2"/><circle cx="6.5" cy="16.5" r="2.5"/><circle cx="16.5" cy="16.5" r="2.5"/></svg>',
        'cart' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="'.$c.'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>',
        'hotel' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="'.$c.'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2Z"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>',
        'billing' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="'.$c.'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>',
        'crm' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="'.$c.'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        'wrench' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="'.$c.'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
        'search' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="'.$c.'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>',
        'share' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="'.$c.'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>',
        'megaphone' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="'.$c.'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 18-5v12L3 13v-2z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>',
        'palette' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="'.$c.'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="13.5" cy="6.5" r=".5"/><circle cx="17.5" cy="10.5" r=".5"/><circle cx="8.5" cy="7.5" r=".5"/><circle cx="6.5" cy="12.5" r=".5"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"/></svg>',
        'globe' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="'.$c.'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
    ];
    return $icons[$icon] ?? $icons['globe'];
}

/* ── Chatbot Data Provider ───────────────────────────── */
function getChatbotData(string $locale): array {
    try {
        $db = getDB();
        
        // Fetch all nodes with their translations
        $stmt = $db->prepare('SELECT n.id, n.is_root, n.reply_type, n.input_var_name, nt.message 
            FROM chatbot_nodes n 
            JOIN chatbot_node_translations nt ON n.id = nt.node_id AND nt.locale = ?');
        $stmt->execute([$locale]);
        $nodes = $stmt->fetchAll();

        // Fetch all options with their translations
        $optStmt = $db->prepare('SELECT o.id, o.node_id, o.next_node_id, o.action_type, o.action_value, o.sort_order, ot.label
            FROM chatbot_options o
            JOIN chatbot_option_translations ot ON o.id = ot.option_id AND ot.locale = ?
            ORDER BY o.sort_order ASC');
        $optStmt->execute([$locale]);
        $options = $optStmt->fetchAll(PDO::FETCH_ASSOC);

        $nodeMap = [];
        $startNodeId = null;

        foreach ($nodes as $node) {
            if ($node['is_root']) {
                $startNodeId = $node['id'];
            }
            $nodeMap[$node['id']] = [
                'message' => $node['message'],
                'reply_type' => $node['reply_type'] ?? 'preset',
                'input_var_name' => $node['input_var_name'] ?? '',
                'options' => []
            ];
        }

        foreach ($options as $opt) {
            if (isset($nodeMap[$opt['node_id']])) {
                $nodeMap[$opt['node_id']]['options'][] = [
                    'id' => $opt['id'],
                    'label' => $opt['label'],
                    'action' => $opt['action_type'],
                    'target' => $opt['action_type'] === 'goto_node' ? $opt['next_node_id'] : $opt['action_value']
                ];
            }
        }

        return [
            'start_node_id' => $startNodeId,
            'nodes' => $nodeMap
        ];
    } catch (Exception $e) {
        return ['start_node_id' => null, 'nodes' => []];
    }
}

/* ── Team Members from DB ──────────────────────────────── */
function getTeamMembers(): array {
    try {
        $db = getDB();
        $locale = getCurrentLocale();
        $stmt = $db->prepare('SELECT m.*, t.name, t.role, t.bio
            FROM team_members m
            LEFT JOIN team_member_translations t ON m.id = t.member_id AND t.locale = ?
            WHERE m.is_active = 1
            ORDER BY m.sort_order');
        $stmt->execute([$locale]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/* ── Testimonials from DB ──────────────────────────────── */
function getTestimonials(): array {
    try {
        $db = getDB();
        $locale = getCurrentLocale();
        $stmt = $db->prepare('SELECT t.*, tt.client_name, tt.client_company, tt.content
            FROM testimonials t
            LEFT JOIN testimonial_translations tt ON t.id = tt.testimonial_id AND tt.locale = ?
            WHERE t.is_active = 1
            ORDER BY t.sort_order');
        $stmt->execute([$locale]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}
