<?php
$servername = "db";
$username = "user";
$password = "test";
$dbname = "todo_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// --- BACKEND LOGIC ---

// 1. Add Task
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add_task') {
    $title = $conn->real_escape_string($_POST['title']);
    $conn->query("INSERT INTO tasks (title) VALUES ('$title')");
}

// 2. Add Note
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add_note') {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $conn->query("INSERT INTO notes (title, content) VALUES ('$title', '$content')");
}

// 3. Delete Item
if (isset($_GET['delete_task'])) {
    $id = intval($_GET['delete_task']);
    $conn->query("DELETE FROM tasks WHERE id=$id");
}
if (isset($_GET['delete_note'])) {
    $id = intval($_GET['delete_note']);
    $conn->query("DELETE FROM notes WHERE id=$id");
}

// 4. Toggle Task Status
if (isset($_GET['toggle_task'])) {
    $id = intval($_GET['toggle_task']);
    $conn->query("UPDATE tasks SET status = NOT status WHERE id=$id");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevOps Dashboard</title>
    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    
    <style>
        :root {
            /* Material 3 Color Tokens */
            --md-sys-color-primary: #6750A4;
            --md-sys-color-on-primary: #FFFFFF;
            --md-sys-color-primary-container: #EADDFF;
            --md-sys-color-background: #FFEFBD; /* Light yellow background */
            --md-sys-color-surface: #FEF7FF;
            --md-sys-color-surface-variant: #E7E0EC;
            --md-radius-l: 16px;
            --md-radius-xl: 28px;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--md-sys-color-background);
            margin: 0;
            display: flex;
            justify-content: center;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 600px;
            background: var(--md-sys-color-surface);
            margin-top: 20px;
            border-radius: var(--md-radius-xl);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 90vh;
        }

        /* Header */
        .header {
            background-color: var(--md-sys-color-primary);
            color: var(--md-sys-color-on-primary);
            padding: 24px;
            text-align: center;
        }
        .header h1 { margin: 0; font-weight: 500; font-size: 24px; }

        /* Tabs */
        .tabs {
            display: flex;
            background: var(--md-sys-color-surface-variant);
            cursor: pointer;
        }
        .tab {
            flex: 1;
            padding: 16px;
            text-align: center;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 3px solid transparent;
            transition: 0.3s;
        }
        .tab.active {
            border-bottom: 3px solid var(--md-sys-color-primary);
            color: var(--md-sys-color-primary);
            background: rgba(103, 80, 164, 0.05);
        }

        /* Content Area */
        .content {
            padding: 20px;
            overflow-y: auto;
            flex-grow: 1;
        }
        .page { display: none; }
        .page.active { display: block; }

        /* Input Forms */
        .input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        input, textarea {
            flex-grow: 1;
            padding: 12px 16px;
            border: 1px solid #ccc;
            border-radius: var(--md-radius-l);
            background: #fff;
            font-family: inherit;
        }
        textarea { resize: vertical; min-height: 60px; }
        
        button {
            background-color: var(--md-sys-color-primary);
            color: white;
            border: none;
            padding: 0 24px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        button:hover { opacity: 0.9; }

        /* Lists */
        .card {
            background: white;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .card:hover { transform: translateY(-2px); }

        .task-done { text-decoration: line-through; color: #888; }
        
        .note-card {
            flex-direction: column;
            align-items: flex-start;
        }
        .note-title { font-weight: bold; margin-bottom: 5px; color: var(--md-sys-color-primary); }
        .note-content { font-size: 0.9rem; color: #444; white-space: pre-wrap; }

        .icon-btn {
            background: none;
            color: #666;
            padding: 5px;
            cursor: pointer;
        }
        .icon-btn:hover { color: #d32f2f; background: none; }
        .check-btn:hover { color: #2e7d32; }

    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>🚀 DevOps Lab Manager</h1>
    </div>

    <div class="tabs">
        <div class="tab active" onclick="switchTab('tasks')">To-Do List</div>
        <div class="tab" onclick="switchTab('notes')">Field Notes</div>
    </div>

    <!-- TASKS TAB -->
    <div id="tasks" class="content page active">
        <form method="POST" class="input-group">
            <input type="hidden" name="action" value="add_task">
            <input type="text" name="title" placeholder="What needs to be done?" required>
            <button type="submit"><span class="material-icons-outlined">add</span></button>
        </form>

        <?php
        $result = $conn->query("SELECT * FROM tasks ORDER BY id DESC");
        while($row = $result->fetch_assoc()):
        ?>
            <div class="card">
                <span class="<?php echo $row['status'] ? 'task-done' : ''; ?>">
                    <?php echo htmlspecialchars($row['title']); ?>
                </span>
                <div style="display:flex;">
                    <a href="?toggle_task=<?php echo $row['id']; ?>" class="icon-btn check-btn">
                        <span class="material-icons-outlined"><?php echo $row['status'] ? 'check_circle' : 'radio_button_unchecked'; ?></span>
                    </a>
                    <a href="?delete_task=<?php echo $row['id']; ?>" class="icon-btn">
                        <span class="material-icons-outlined">delete</span>
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- NOTES TAB -->
    <div id="notes" class="content page">
        <form method="POST" style="margin-bottom: 20px;">
            <input type="hidden" name="action" value="add_note">
            <div class="input-group">
                <input type="text" name="title" placeholder="Note Title" required>
            </div>
            <div class="input-group">
                <textarea name="content" placeholder="Write your observation..." required></textarea>
                <button type="submit">Save</button>
            </div>
        </form>

        <?php
        $result = $conn->query("SELECT * FROM notes ORDER BY id DESC");
        while($row = $result->fetch_assoc()):
        ?>
            <div class="card note-card">
                <div style="width:100%; display:flex; justify-content:space-between;">
                    <span class="note-title"><?php echo htmlspecialchars($row['title']); ?></span>
                    <a href="?delete_note=<?php echo $row['id']; ?>" class="icon-btn">
                        <span class="material-icons-outlined">close</span>
                    </a>
                </div>
                <div class="note-content"><?php echo htmlspecialchars($row['content']); ?></div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
    function switchTab(tabId) {
        // Remove active class from all tabs and pages
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
        
        // Add active class to clicked tab and corresponding page
        document.querySelector(`[onclick="switchTab('${tabId}')"]`).classList.add('active');
        document.getElementById(tabId).classList.add('active');
    }
</script>

</body>
</html>
