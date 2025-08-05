<?php
// Database connection details
$host = '';
$username = '';
$password = '';
$database = '';

$word_rows = [];
$db_error = null;

// Connect and fetch words
if ($host && $username && $database) {
    try {
        $conn = @new mysqli($host, $username, $password, $database);
        if ($conn->connect_errno) {
            $db_error = "Could not connect to the dictionary database.";
        } else {
            $sql = "SELECT word, definition FROM langman_words";
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $word_rows[] = $row;
                }
            }
            $conn->close();
        }
    } catch (Exception $e) {
        $db_error = "Could not connect to the dictionary database.";
    }
} else {
    $db_error = "Database settings are not configured.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="dictionary, ancient, language, alagaesia, eragon, eldest, brisingr, inheritance, inheritance cycle, mmorpg, dragon, paolini, christopher, mcalagaesia, minecraft, arcaena, mmo, rpg, game">
    <meta name="title" content="Ancient Language Dictionary">
    <meta name="description" content="A lexicon of ancient language words from the World of Eragon.">
    <meta name="author" content="MCAlagaesia">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://brisin.gr/dictionary/">
    <meta property="og:title" content="Ancient Language Dictionary">
    <meta property="og:description" content="A lexicon of ancient language words from the World of Eragon.">
    <meta property="og:image" content="https://brisin.gr/tweetstorm/img/metadata.jpg">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://brisin.gr/dictionary/">
    <meta property="twitter:title" content="Ancient Language Dictionary">
    <meta property="twitter:description" content="A lexicon of ancient language words from the World of Eragon.">
    <meta property="twitter:image" content="https://brisin.gr/tweetstorm/img/metadata.jpg">
    
    <link rel="icon" href="../favicon.ico" type="image/x-icon" />
    <title>Ancient Language Dictionary</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
<nav>
    <a href="https://mcalagaesia.com">Back to MCAlagaësia</a>
    <a href="https://discord.gg/EJSaEYd83f">Play Langman</a>
    <a href="https://arcaena.com/discord">Join the Community</a>
</nav>
<div class="site-logo">
    <img src="img/logo-arc.png" alt="MCAlagaësia Logo">
</div>
<div class="words-container">
    <h1>Ancient Language Dictionary</h1>
    <p style="font-size:1.15em;margin-bottom:22px;color:#eee;text-shadow:0 2px 6px #0007;">
        A lexicon of ancient language words from the World of Eragon.
    </p>
    <div class="table-search-container">
        <input
            type="search"
            id="word-search-input"
            class="table-search-input"
            placeholder="Search">
        <span class="search-icon" aria-hidden="true">&#128269;</span>
    </div>

    <div style="overflow-x:auto;">
        <table id="words_table" class="table-sort">
            <thead>
                <tr>
                    <th id="th-word" onclick="toggleSort('word')" tabindex="0" aria-sort="none"
                        onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();toggleSort('word');}">
                        <span class="header-flex">
                            Ancient Word <span class="sort-arrow" id="word_sortarrow">↓</span>
                        </span>
                    </th>
                    <th id="th-definition" onclick="toggleSort('definition')" tabindex="0" aria-sort="none"
                        onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();toggleSort('definition');}">
                        <span class="header-flex">
                            English Translation <span class="sort-arrow" id="def_sortarrow">↓</span>
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows rendered by JS -->
            </tbody>
        </table>
    </div>
</div>
<div id="below-fold">
        <div class="content-columns">
            <div>
                <h2>About</h2>
                <p>The MCAlagaësia Project seeks to re-create Eragon's world of Alagaësia from the Inheritance Cycle by Christopher Paolini in Minecraft.</p>
            </div>
            <div>
                <h2>Contact</h2>
                <p>
                    <img src="https://mcalagaesia.com/img/discord-icon.webp" alt="Discord Icon" width="25">
                    <a href="https://arcaena.com/discord">https://arcaena.com/discord</a>
                </p>
                <p>
                    <img src="https://mcalagaesia.com/img/mail-icon.webp" alt="Email Icon" width="25">
                    <a href="mailto:contact@mcalagaesia.com">contact@mcalagaesia.com</a>
                </p>
            </div>
            <div>
                <h2>Copyright &copy; MCAlagaësia <?= date('Y') ?></h2>
                <p class="legal">The Inheritance Cycle and the World of Eragon are the property of Christopher Paolini and affiliated publishers.</p>
                <p class="legal">MCAlagaësia is not an official Minecraft product. MCAlagaësia is not associated with Mojang or Microsoft.</p>
            </div>
        </div>
    </div> 
<script>
const wordRows = <?php echo json_encode($word_rows, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
wordRows.push({word: 'invalid entry', definition: 'the name of the ancient language'});
let sortState = { word: false, definition: false };
let lastSort = 'word';
let filterValue = '';

function escapeHTML(text) {
    return ('' + text)
        .replace(/&/g, "&amp;").replace(/</g, "&lt;")
        .replace(/>/g, "&gt;").replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function accentFold(str) {
    return str.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
}

// Performs filtering (and sorting) before rendering
function getFilteredRows() {
    let rows = wordRows;
    if (filterValue) {
        const v = accentFold(filterValue.toLowerCase());
        rows = rows.filter(row =>
            accentFold(row.word.toLowerCase()).includes(v) ||
            accentFold(row.definition.toLowerCase()).includes(v)
        );
    }
    let sorted = rows.slice();
    sorted.sort((a, b) => {
        let col = lastSort;
        let res = a[col].localeCompare(b[col], 'en', {sensitivity:'base'});
        return sortState[col] ? res : -res;
    });
    return sorted;
}

function renderTable(data) {
    const tbody = document.getElementById('words_table').getElementsByTagName('tbody')[0];
    tbody.innerHTML = '';
    if (!data.length) {
        <?php if ($db_error): ?>
        // If DB is down, show a message in the table body
        const row = document.createElement('tr');
        row.innerHTML = '<td colspan="2" style="color:#ffc; background:#711c2e99;">Dictionary currently unavailable. Please try again later.</td>';
        tbody.appendChild(row);
        <?php else: ?>
        const row = document.createElement('tr');
        row.innerHTML = '<td colspan="2">No words found.</td>';
        tbody.appendChild(row);
        <?php endif; ?>
        return;
    }
    data.forEach(row => {
        const tr = document.createElement('tr');
        const wordHtml = filterValue ? highlightMatch(row.word, filterValue) : escapeHTML(row.word);
        const defHtml = filterValue ? highlightMatch(row.definition, filterValue) : escapeHTML(row.definition);
        tr.innerHTML = `
            <td class="td-copiable" data-label="Ancient Word">${wordHtml}
                <span class="copy-tooltip">Click to copy</span>
            </td>
            <td class="td-copiable" data-label="English Translation">${defHtml}
                <span class="copy-tooltip">Click to copy</span>
            </td>`;
        tbody.appendChild(tr);
    });

    initialiseCopyToClipboard();
}

function setAriaSort(column, direction) {
    document.getElementById('th-word').setAttribute('aria-sort', column === 'word' ? (direction ? "ascending" : "descending") : "none");
    document.getElementById('th-definition').setAttribute('aria-sort', column === 'definition' ? (direction ? "ascending" : "descending") : "none");
}

function toggleSort(column) {
    if (lastSort === column) {
        sortState[column] = !sortState[column];
    } else {
        sortState[column] = true;
        lastSort = column;
    }
    updateSortIndicators(column);
    renderTable(getFilteredRows());
}

function updateSortIndicators(activeCol) {
    document.getElementById('th-word').classList.toggle('active-sort', activeCol === 'word');
    document.getElementById('th-definition').classList.toggle('active-sort', activeCol === 'definition');
    document.getElementById('word_sortarrow').textContent = (activeCol === 'word') ? (sortState.word ? '↑' : '↓') : '↓';
    document.getElementById('def_sortarrow').textContent = (activeCol === 'definition') ? (sortState.definition ? '↑' : '↓') : '↓';
    setAriaSort(activeCol, sortState[activeCol]);
}

document.addEventListener('DOMContentLoaded', function () {
    // Initial sort and render
    toggleSort('word');

    // --- Search Bar Handler ---
    const searchInput = document.getElementById('word-search-input');
    searchInput.addEventListener('input', function () {
        filterValue = this.value;
        renderTable(getFilteredRows());
    });
});

function initialiseCopyToClipboard() {
    // Handles all .td-copiable cells
    const showDelay = 380; // ms to show tooltip after hover
    const hideDelay = 250;
    let tooltipTimers = new WeakMap();

    document.querySelectorAll('.td-copiable').forEach(td => {
        const tooltip = td.querySelector('.copy-tooltip');
        let showTimer = null;
        let hideTimer = null;

        // --- Hover (show after delay) ---
        td.addEventListener('mouseenter', function () {
            showTimer = setTimeout(() => {
                tooltip.style.visibility = "visible";
                tooltip.style.opacity = "1";
            }, showDelay);
            tooltipTimers.set(td, showTimer);
        });

        td.addEventListener('mouseleave', function () {
            if (showTimer) clearTimeout(showTimer);
            tooltip.style.opacity = "0";
            tooltip.style.visibility = "hidden";
        });

        // --- Click copy behaviour ---
        td.addEventListener('click', function (e) {
            // Get cell's *raw text* (not including tooltip), remove leading/trailing whitespace.
            let valToCopy = td.childNodes[0].textContent.trim();
            // Copy to clipboard
            navigator.clipboard.writeText(valToCopy).then(() => {
                tooltip.textContent = "Copied!";
                tooltip.style.visibility = "visible";
                tooltip.style.opacity = "1";
                td.classList.add('td-canonical-copied');
                setTimeout(() => {
                    tooltip.textContent = "Click to copy";
                    tooltip.style.opacity = "0";
                    tooltip.style.visibility = "hidden";
                    td.classList.remove('td-canonical-copied');
                }, 1100);
            });
        });

        // --- Keyboard accessibility: enter/space to copy ---
        td.setAttribute('tabindex', '0');
        td.setAttribute('role', 'button');
        td.setAttribute('aria-label', 'Click to copy');
        td.addEventListener('keydown', function (e) {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                td.click();
            }
        });
    });
}

document.addEventListener('keydown', function(e) {
    if (
        (e.ctrlKey || e.metaKey) && 
        (e.key === 'f' || e.key === 'F') &&
        !e.shiftKey && !e.altKey && !e.isComposing
    ) {
        e.preventDefault();
        const input = document.getElementById('word-search-input');
        if (input) {
            input.focus();
            input.select();
        }
    }
});

function highlightMatch(text, query) {
    if (!query) return escapeHTML(text);
    const normText = accentFold(text);
    const normQuery = accentFold(query);
    const regex = new RegExp(normQuery.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi');
    let result = '';
    let lastIndex = 0;
    let match;

    while ((match = regex.exec(normText)) !== null) {
        const start = match.index;
        const end = regex.lastIndex;
        result += escapeHTML(text.slice(lastIndex, start));
        result += '<mark>' + escapeHTML(text.slice(start, end)) + '</mark>';
        lastIndex = end;
    }
    result += escapeHTML(text.slice(lastIndex));
    return result;
}
</script>
<script type="text/javascript" src="js/main.js"></script>
</body>
</html>
