<?php
if (!defined('APP_NAME')) die('Direct access prevented');
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title>API Documentation — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
    <style>
        .endpoint-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 0.75rem;
            margin-bottom: 2rem;
            overflow: hidden;
        }
        .endpoint-header {
            background: rgba(0, 0, 0, 0.2);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .method-badge {
            font-size: 0.75rem;
            font-weight: 800;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            text-transform: uppercase;
        }
        .method-post { background: rgba(16, 185, 129, 0.2); color: #10b981; }
        .method-get { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
        
        .endpoint-body {
            padding: 1.5rem;
        }
        pre {
            background: #0f1219 !important;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.5rem;
            padding: 1rem;
            overflow-x: auto;
            color: #a5b4fc;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
        .param-table {
            width: 100%;
            text-align: left;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .param-table th, .param-table td {
            padding: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .param-table th {
            font-weight: 600;
            color: #94a3b8;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .param-table td {
            font-size: 0.875rem;
            color: #e2e8f0;
        }
        .type-badge {
            font-family: monospace;
            color: #cbd5e1;
            background: rgba(255, 255, 255, 0.1);
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
        }
    </style>
</head>
<body class="bg-[#0b0e14] text-white">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php $currentPage = 'api-docs'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/5 via-transparent to-transparent"></div>
            <div class="relative flex items-center gap-4">
                <a href="<?= baseUrl('admin/app-products') ?>" class="w-10 h-10 rounded-xl bg-white/5 hover:bg-white/10 flex items-center justify-center border border-white/10 transition-colors">
                    <i class="ph ph-arrow-left text-xl text-white/70"></i>
                </a>
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center border border-blue-500/20">
                    <i class="ph ph-code text-2xl text-blue-500"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-tight">API Documentation</h1>
                    <p class="text-[10px] text-white/40 uppercase tracking-widest font-black hidden sm:block">Developer Integration Guide</p>
                </div>
            </div>
            <div class="relative z-10 flex items-center gap-4">
                <?php require __DIR__ . '/partials/_topbar.php'; ?>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 crm-main-scroll bg-[#0b0e14]">
            
            <div class="max-w-4xl mx-auto">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-white mb-2">Integration Overview</h2>
                    <p class="text-white/60 text-sm leading-relaxed">
                        These APIs allow you to build desktop, mobile, or web applications that securely verify licenses, track hardware IDs, and collect telemetry data directly into the Mico Sage ecosystem. All endpoints accept and return JSON.
                    </p>
                </div>

                <!-- ENDPOINT 1 -->
                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <span class="method-badge method-post">POST</span>
                        <code class="text-lg font-mono text-white"><?= baseUrl('api/v1/license') ?></code>
                    </div>
                    <div class="endpoint-body">
                        <p class="text-sm text-white/70 mb-4">Verifies a license key, establishes a hardware binding if configured, and returns the license entitlement details.</p>
                        
                        <h4 class="text-sm font-bold text-white mb-2 mt-6">Request Body (JSON)</h4>
                        <table class="param-table mb-4">
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Type</th>
                                    <th>Required</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>license_key</code></td>
                                    <td><span class="type-badge">string</span></td>
                                    <td><span class="text-emerald-400">Yes</span></td>
                                    <td>The unique license key provided to the customer.</td>
                                </tr>
                                <tr>
                                    <td><code>hardware_id</code></td>
                                    <td><span class="type-badge">string</span></td>
                                    <td><span class="text-amber-400">Optional*</span></td>
                                    <td>Unique device ID (e.g., Motherboard UUID). *Required if the license has `bound_hardware_id` feature enabled.</td>
                                </tr>
                                <tr>
                                    <td><code>hostname</code></td>
                                    <td><span class="type-badge">string</span></td>
                                    <td>No</td>
                                    <td>The computer's friendly name (for telemetry).</td>
                                </tr>
                                <tr>
                                    <td><code>app_version</code></td>
                                    <td><span class="type-badge">string</span></td>
                                    <td>No</td>
                                    <td>The version of your application currently running.</td>
                                </tr>
                            </tbody>
                        </table>

                        <h4 class="text-sm font-bold text-white mb-2 mt-6">Success Response</h4>
                        <pre>{
  "status": "ACTIVE",
  "type": "pro",
  "product": "Forensic Recovery Pro",
  "max_devices": 1,
  "use_count": 0,
  "max_use_count": -1,
  "expires_at": "2027-12-31 23:59:59",
  "features": {
    "bound_hardware_id": "HW-12345-ABCDE",
    "advanced_recovery": "true"
  },
  "customer_name": "John Doe",
  "about_text": "Mico Sage Forensic Tool\nLicensed to: John Doe"
}</pre>
                        
                        <h4 class="text-sm font-bold text-white mb-2 mt-6">Understanding License Limits</h4>
                        <p class="text-sm text-white/70 mb-4 text-justify">
                            <strong class="text-white"><code>max_use_count</code></strong>: Determines the total number of operations (e.g., successful recoveries) allowed by this license. A value of <code>-1</code> means unlimited usage.<br>
                            <strong class="text-white"><code>use_count</code></strong>: The current number of operations already performed. You must implement local tracking and sync this count via the Heartbeat API.<br>
                            <strong class="text-white"><code>expires_at</code></strong>: A datetime string indicating when the license expires. If the license is permanent, this may be null.
                        </p>
                        
                        <h4 class="text-sm font-bold text-white mb-2 mt-6">Error Responses</h4>
                        <pre>{"status": "invalid", "error": "Invalid license key"} // 404 Not Found
{"status": "invalid", "error": "Invalid key. Please check your purchase details."} // 403 Forbidden (Hardware Mismatch)
{"status": "expired", "message": "License has expired"} // 200 OK but expired</pre>
                    </div>
                </div>

                <!-- ENDPOINT 2 -->
                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <span class="method-badge method-post">POST</span>
                        <code class="text-lg font-mono text-white"><?= baseUrl('api/v1/heartbeat') ?></code>
                    </div>
                    <div class="endpoint-body">
                        <p class="text-sm text-white/70 mb-4">Sends telemetry data, checks if the license was remotely revoked/expired, and tracks usage counts or active devices.</p>
                        
                        <h4 class="text-sm font-bold text-white mb-2 mt-6">Request Body (JSON)</h4>
                        <table class="param-table mb-4">
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Type</th>
                                    <th>Required</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>license_key</code></td>
                                    <td><span class="type-badge">string</span></td>
                                    <td><span class="text-emerald-400">Yes</span></td>
                                    <td>The unique license key.</td>
                                </tr>
                                <tr>
                                    <td><code>hardware_id</code></td>
                                    <td><span class="type-badge">string</span></td>
                                    <td><span class="text-emerald-400">Yes</span></td>
                                    <td>Unique device ID. Used to enforce device limits and track online status.</td>
                                </tr>
                                <tr>
                                    <td><code>use_count</code></td>
                                    <td><span class="type-badge">integer</span></td>
                                    <td>No</td>
                                    <td>Send the cumulative amount of "operations" the app has performed, to sync usage limits.</td>
                                </tr>
                            </tbody>
                        </table>

                        <h4 class="text-sm font-bold text-white mb-2 mt-6">Success Response</h4>
                        <pre>{
  "status": "active",
  "type": "standard",
  "is_new_device": false,
  "message": "OK"
}</pre>
                    </div>
                </div>
                
                <!-- ENDPOINT 3 -->
                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <span class="method-badge method-get">GET</span>
                        <code class="text-lg font-mono text-white"><?= baseUrl('api/download-track.php?id={ID}') ?></code>
                    </div>
                    <div class="endpoint-body">
                        <p class="text-sm text-white/70 mb-4">Tracks an app download and redirects the user to the actual secure file URL. Use this link in emails or website buttons.</p>
                        
                        <h4 class="text-sm font-bold text-white mb-2 mt-6">URL Parameters</h4>
                        <table class="param-table mb-4">
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Type</th>
                                    <th>Required</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>id</code></td>
                                    <td><span class="type-badge">integer</span></td>
                                    <td><span class="text-emerald-400">Yes</span></td>
                                    <td>The database ID of the App Product.</td>
                                </tr>
                            </tbody>
                        </table>

                        <p class="text-sm text-white/70 mt-4"><strong>Response:</strong> Returns a <code class="bg-[#0f1219] px-1 py-0.5 rounded text-white border border-white/10">302 Found</code> redirect to the actual file location specified in the product's <code>download_url</code>.</p>
                    </div>
                </div>

            </div>
        </main>
    </div>
</div>
</body>
</html>
