<?php
$content = file_get_contents('controllers/AdminController.php');
$tokens = token_get_all($content);
$stack = [];
foreach ($tokens as $token) {
    if (is_array($token)) {
        if ($token[0] === T_CURLY_OPEN || $token[0] === T_DOLLAR_OPEN_CURLY_BRACES) {
            $stack[] = $token[2];
        }
    } else {
        if ($token === '{') {
            // Find line from surrounding tokens? tokens after 5.2.2 have line in index 2
            // but for strings it's not there. 
            // We can track lines manually.
        }
    }
}

// Final approach: manual scan with line tracking
$stack = [];
$chars = str_split($content);
$line = 1;
$inString = false;
$quote = '';
$inComment = false;
$commentType = ''; // 'block' or 'line'

for ($i = 0; $i < count($chars); $i++) {
    $c = $chars[$i];
    $next = $chars[$i+1] ?? '';
    
    if ($c === "\n") $line++;

    if (!$inString && !$inComment) {
        if ($c === '"' || $c === "'") {
            $inString = true;
            $quote = $c;
        } elseif ($c === '/' && $next === '*') {
            $inComment = true;
            $commentType = 'block';
            $i++;
        } elseif ($c === '/' && $next === '/') {
            $inComment = true;
            $commentType = 'line';
            $i++;
        } elseif ($c === '{') {
            $stack[] = $line;
        } elseif ($c === '}') {
            if (empty($stack)) {
                echo "Unmatched '}' at line $line\n";
            } else {
                array_pop($stack);
            }
        }
    } elseif ($inString) {
        if ($c === $quote && $chars[$i-1] !== "\\") {
            $inString = false;
        }
    } elseif ($inComment) {
        if ($commentType === 'block' && $c === '*' && $next === '/') {
            $inComment = false;
            $i++;
        } elseif ($commentType === 'line' && $c === "\n") {
            $inComment = false;
        }
    }
}

if (!empty($stack)) {
    echo "Unclosed '{' starting at lines: " . implode(', ', $stack) . "\n";
} else {
    echo "Balanced.\n";
}
