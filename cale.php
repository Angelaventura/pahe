<?php
// calendar.php - Calendario completo con API y frontend
session_start();

// Configuración
$tasksFile = 'tasks.json';

// Función para cargar tareas
function loadTasks($file) {
    if (!file_exists($file)) {
        file_put_contents($file, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return [];
    }
    $json = file_get_contents($file);
    return json_decode($json, true) ?: [];
}

// Función para guardar tareas
function saveTasks($file, $tasks) {
    $json = json_encode($tasks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($file, $json) !== false;
}

// Manejar peticiones AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $tasks = loadTasks($tasksFile);
    $response = ['success' => false, 'message' => 'Acción no válida'];
    
    switch ($_POST['action']) {
        case 'get_tasks':
            $response = ['success' => true, 'tasks' => $tasks];
            break;
            
        case 'create_task':
            $newTask = [
                'id' => time() . rand(1000, 9999),
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? '',
                'date' => $_POST['date'] ?? '',
                'time' => $_POST['time'] ?? '',
                'priority' => $_POST['priority'] ?? 'low',
                'completed' => false,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $tasks[] = $newTask;
            if (saveTasks($tasksFile, $tasks)) {
                $response = ['success' => true, 'task' => $newTask, 'message' => 'Tarea creada exitosamente'];
            } else {
                $response = ['success' => false, 'message' => 'Error al guardar la tarea'];
            }
            break;
            
        case 'update_task':
            $id = $_POST['id'] ?? '';
            $found = false;
            
            foreach ($tasks as &$task) {
                if ($task['id'] == $id) {
                    $task['title'] = $_POST['title'] ?? $task['title'];
                    $task['description'] = $_POST['description'] ?? $task['description'];
                    $task['date'] = $_POST['date'] ?? $task['date'];
                    $task['time'] = $_POST['time'] ?? $task['time'];
                    $task['priority'] = $_POST['priority'] ?? $task['priority'];
                    $task['completed'] = isset($_POST['completed']) ? (bool)$_POST['completed'] : $task['completed'];
                    $task['updated_at'] = date('Y-m-d H:i:s');
                    $found = true;
                    break;
                }
            }
            
            if ($found && saveTasks($tasksFile, $tasks)) {
                $response = ['success' => true, 'message' => 'Tarea actualizada exitosamente'];
            } else {
                $response = ['success' => false, 'message' => 'Error al actualizar la tarea'];
            }
            break;
            
        case 'delete_task':
            $id = $_POST['id'] ?? '';
            $originalCount = count($tasks);
            
            $tasks = array_filter($tasks, function($task) use ($id) {
                return $task['id'] != $id;
            });
            
            $tasks = array_values($tasks); // Reindexar array
            
            if (count($tasks) < $originalCount && saveTasks($tasksFile, $tasks)) {
                $response = ['success' => true, 'message' => 'Tarea eliminada exitosamente'];
            } else {
                $response = ['success' => false, 'message' => 'Error al eliminar la tarea'];
            }
            break;
            
        case 'toggle_complete':
            $id = $_POST['id'] ?? '';
            $found = false;
            
            foreach ($tasks as &$task) {
                if ($task['id'] == $id) {
                    $task['completed'] = !$task['completed'];
                    $task['updated_at'] = date('Y-m-d H:i:s');
                    $found = true;
                    break;
                }
            }
            
            if ($found && saveTasks($tasksFile, $tasks)) {
                $response = ['success' => true, 'message' => 'Estado de tarea actualizado'];
            } else {
                $response = ['success' => false, 'message' => 'Error al actualizar el estado'];
            }
            break;
    }
    
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario Gestor de Tareas - Estilo Notion</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: ui-sans-serif, -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, "Apple Color Emoji", Arial, sans-serif, "Segoe UI Emoji", "Segoe UI Symbol";
            background-color: #f7f6f3;
            color: #37352f;
            line-height: 1.5;
        }

        .container {
            max-width: 100%;
            margin: 0;
            background: white;
            min-height: 100vh;
        }

        .header {
            background: white;
            border-bottom: 1px solid #e9e9e7;
            padding: 16px 24px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .header-title {
            font-size: 22px;
            font-weight: 600;
            color: #37352f;
        }

        .view-toggle {
            display: flex;
            background: #f1f1ef;
            border-radius: 6px;
            padding: 2px;
        }

        .view-btn {
            padding: 6px 12px;
            border: none;
            background: transparent;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: #787774;
            transition: all 0.2s;
        }

        .view-btn.active {
            background: white;
            color: #37352f;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .calendar-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-btn {
            background: none;
            border: none;
            padding: 8px;
            border-radius: 4px;
            cursor: pointer;
            color: #787774;
            font-size: 18px;
            transition: all 0.2s;
        }

        .nav-btn:hover {
            background: #f1f1ef;
        }

        .current-date {
            font-size: 18px;
            font-weight: 600;
            color: #37352f;
        }

        .add-task-btn {
            background: #2383e2;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .add-task-btn:hover {
            background: #1a73d1;
        }

        .calendar-content {
            padding: 0 24px 24px;
        }

        .today-section {
            background: white;
            border-top: 1px solid #e9e9e7;
            padding: 24px;
            margin-top: 24px;
        }

        .today-header {
            font-size: 18px;
            font-weight: 600;
            color: #37352f;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .today-date {
            font-size: 14px;
            font-weight: 400;
            color: #787774;
        }

        .today-tasks {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .today-task {
            background: #f8f8f7;
            border: 1px solid #e9e9e7;
            border-radius: 8px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
        }

        .today-task:hover {
            background: #f1f1ef;
            border-color: #d9d9d6;
        }

        .today-task.completed {
            background: #f0f9f0;
            border-color: #4caf50;
            opacity: 0.8;
        }

        .task-checkbox {
            width: 20px;
            height: 20px;
            border: 2px solid #c0beb9;
            border-radius: 4px;
            background: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .task-checkbox:hover {
            border-color: #2383e2;
        }

        .task-checkbox.completed {
            background: #4caf50;
            border-color: #4caf50;
            color: white;
        }

        .task-checkbox.completed::after {
            content: '✓';
            font-size: 14px;
            font-weight: bold;
        }

        .task-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .task-title-today {
            font-size: 16px;
            font-weight: 500;
            color: #37352f;
            margin: 0;
        }

        .task-title-today.completed {
            text-decoration: line-through;
            color: #787774;
        }

        .task-meta {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 14px;
            color: #787774;
        }

        .task-time {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .task-priority-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            font-weight: 500;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .task-priority-badge.high {
            background: #fef2f2;
            color: #dc2626;
        }

        .task-priority-badge.medium {
            background: #fef3c7;
            color: #d97706;
        }

        .task-priority-badge.low {
            background: #f1f5f9;
            color: #64748b;
        }

        .task-actions {
            display: flex;
            gap: 8px;
        }

        .task-action-btn {
            background: none;
            border: none;
            padding: 6px;
            border-radius: 4px;
            cursor: pointer;
            color: #787774;
            font-size: 16px;
            transition: all 0.2s;
        }

        .task-action-btn:hover {
            background: #e9e9e7;
            color: #37352f;
        }

        .no-tasks {
            text-align: center;
            color: #787774;
            font-style: italic;
            padding: 32px;
        }

        .week-view {
            display: grid;
            grid-template-columns: 80px repeat(7, 1fr);
            gap: 1px;
            background: #e9e9e7;
            border-radius: 8px;
            overflow: hidden;
        }

        .time-column {
            background: white;
            padding: 12px 8px;
            text-align: center;
            font-size: 12px;
            color: #787774;
            border-right: 1px solid #e9e9e7;
        }

        .day-header {
            background: #f8f8f7;
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            font-size: 14px;
            color: #37352f;
        }

        .day-cell {
            background: white;
            min-height: 100px;
            padding: 8px;
            position: relative;
            border-bottom: 1px solid #f1f1ef;
        }

        .day-cell:hover {
            background: #fbfbfa;
        }

        .task {
            background: #2383e2;
            color: white;
            padding: 4px 8px;
            margin: 2px 0;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
            word-wrap: break-word;
        }

        .task:hover {
            background: #1a73d1;
        }

        .task.completed {
            background: #4caf50;
            opacity: 0.7;
            text-decoration: line-through;
        }

        .task.high-priority {
            background: #e74c3c;
        }

        .task.medium-priority {
            background: #f39c12;
        }

        .task.low-priority {
            background: #95a5a6;
        }

        .month-view {
            display: none;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #e9e9e7;
            border-radius: 8px;
            overflow: hidden;
        }

        .month-view.active {
            display: grid;
        }

        .month-day {
            background: white;
            min-height: 120px;
            padding: 8px;
            position: relative;
        }

        .month-day-number {
            font-size: 14px;
            font-weight: 600;
            color: #37352f;
            margin-bottom: 4px;
        }

        .month-day.other-month {
            background: #f8f8f7;
            color: #c0beb9;
        }

        .month-day.today {
            background: #e8f4fd;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 24px;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 600;
            color: #37352f;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #787774;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
        }

        .close-btn:hover {
            background: #f1f1ef;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #37352f;
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e9e9e7;
            border-radius: 6px;
            font-size: 14px;
            background: white;
            transition: border-color 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #2383e2;
        }

        .form-select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e9e9e7;
            border-radius: 6px;
            font-size: 14px;
            background: white;
            cursor: pointer;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
        }

        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #2383e2;
            color: white;
        }

        .btn-primary:hover {
            background: #1a73d1;
        }

        .btn-secondary {
            background: #f1f1ef;
            color: #37352f;
        }

        .btn-secondary:hover {
            background: #e9e9e7;
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .alert {
            padding: 12px;
            margin-bottom: 16px;
            border-radius: 6px;
            font-size: 14px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .header {
                padding: 12px 16px;
            }

            .calendar-content {
                padding: 0 16px 16px;
            }

            .week-view {
                grid-template-columns: 60px repeat(7, 1fr);
            }

            .modal-content {
                margin: 10% auto;
                width: 95%;
            }

            .current-date {
                font-size: 16px;
            }

            .header-title {
                font-size: 20px;
            }
        }

        @media (max-width: 480px) {
            .week-view {
                grid-template-columns: 50px repeat(7, 1fr);
            }

            .day-cell {
                min-height: 80px;
                padding: 4px;
            }

            .task {
                font-size: 11px;
                padding: 2px 4px;
            }

            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-top">
                <h1 class="header-title">📅 Calendario de Tareas</h1>
                <div class="view-toggle">
                    <button class="view-btn active" onclick="switchView('week')">Semana</button>
                    <button class="view-btn" onclick="switchView('month')">Mes</button>
                </div>
            </div>
            <div class="calendar-nav">
                <button class="nav-btn" onclick="previousPeriod()">‹</button>
                <span class="current-date" id="currentDate"></span>
                <button class="nav-btn" onclick="nextPeriod()">›</button>
                <button class="add-task-btn" onclick="openTaskModal()">+ Nueva Tarea</button>
            </div>
        </div>

        <div class="calendar-content">
            <div id="weekView" class="week-view">
                <!-- Columna de horas -->
                <div class="time-column"></div>
                <div class="day-header">Dom</div>
                <div class="day-header">Lun</div>
                <div class="day-header">Mar</div>
                <div class="day-header">Mié</div>
                <div class="day-header">Jue</div>
                <div class="day-header">Vie</div>
                <div class="day-header">Sáb</div>
                
                <!-- Celdas de días -->
                <div class="time-column">Todo el día</div>
                <div class="day-cell" data-date="0" onclick="openTaskModal(0)"></div>
                <div class="day-cell" data-date="1" onclick="openTaskModal(1)"></div>
                <div class="day-cell" data-date="2" onclick="openTaskModal(2)"></div>
                <div class="day-cell" data-date="3" onclick="openTaskModal(3)"></div>
                <div class="day-cell" data-date="4" onclick="openTaskModal(4)"></div>
                <div class="day-cell" data-date="5" onclick="openTaskModal(5)"></div>
                <div class="day-cell" data-date="6" onclick="openTaskModal(6)"></div>
            </div>

            <div id="monthView" class="month-view">
                <!-- Se llenará dinámicamente -->
            </div>
        </div>

        <!-- Sección de tareas de hoy -->
        <div class="today-section">
            <div class="today-header">
                🗓️ Tareas de Hoy
                <span class="today-date" id="todayDate"></span>
            </div>
            <div class="today-tasks" id="todayTasks">
                <!-- Se llenará dinámicamente -->
            </div>
        </div>
    </div>

    <!-- Modal para agregar/editar tareas -->
    <div id="taskModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Nueva Tarea</h3>
                <button class="close-btn" onclick="closeTaskModal()">×</button>
            </div>
            <div id="alertContainer"></div>
            <form id="taskForm">
                <div class="form-group">
                    <label class="form-label">Título de la tarea</label>
                    <input type="text" class="form-input" id="taskTitle" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea class="form-input" id="taskDescription" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Fecha</label>
                    <input type="date" class="form-input" id="taskDate" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Hora</label>
                    <input type="time" class="form-input" id="taskTime">
                </div>
                <div class="form-group">
                    <label class="form-label">Prioridad</label>
                    <select class="form-select" id="taskPriority">
                        <option value="low">Baja</option>
                        <option value="medium">Media</option>
                        <option value="high">Alta</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeTaskModal()">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="deleteBtn" onclick="deleteTask()" style="display: none;">Eliminar</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentDate = new Date();
        let currentView = 'week';
        let tasks = [];
        let editingTaskId = null;

        // Inicializar la aplicación
        function init() {
            loadTasks();
            updateCalendar();
            updateCurrentDate();
            updateTodayDate();
        }

        // Cargar tareas desde el servidor
        async function loadTasks() {
            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=get_tasks'
                });
                
                const data = await response.json();
                if (data.success) {
                    tasks = data.tasks || [];
                    updateTodayTasks();
                } else {
                    showAlert('Error al cargar las tareas', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Error de conexión', 'error');
            }
        }

        // Guardar tarea en el servidor
        async function saveTaskToServer(taskData, isEdit = false) {
            const action = isEdit ? 'update_task' : 'create_task';
            const body = new URLSearchParams({
                action: action,
                ...taskData
            });

            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: body.toString()
                });
                
                const data = await response.json();
                return data;
            } catch (error) {
                console.error('Error:', error);
                return { success: false, message: 'Error de conexión' };
            }
        }

        // Eliminar tarea del servidor
        async function deleteTaskFromServer(taskId) {
            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete_task&id=${taskId}`
                });
                
                const data = await response.json();
                return data;
            } catch (error) {
                console.error('Error:', error);
                return { success: false, message: 'Error de conexión' };
            }
        }

        // Alternar estado de completado
        async function toggleTaskCompletionOnServer(taskId) {
            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=toggle_complete&id=${taskId}`
                });
                
                const data = await response.json();
                return data;
            } catch (error) {
                console.error('Error:', error);
                return { success: false, message: 'Error de conexión' };
            }
        }

        // Mostrar alerta
        function showAlert(message, type = 'success') {
            const alertContainer = document.getElementById('alertContainer');
            const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
            
            alertContainer.innerHTML = `
                <div class="alert ${alertClass}">
                    ${message}
                </div>
            `;
            
            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 3000);
        }

        // Cambiar vista
        function switchView(view) {
            currentView = view;
            document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            if (view === 'week') {
                document.getElementById('weekView').style.display = 'grid';
                document.getElementById('monthView').style.display = 'none';
            } else {
                document.getElementById('weekView').style.display = 'none';
                document.getElementById('monthView').style.display = 'grid';
                renderMonthView();
            }
            
            updateCurrentDate();
        }

        // Navegar períodos
        function previousPeriod() {
            if (currentView === 'week') {
                currentDate.setDate(currentDate.getDate() - 7);
            } else {
                currentDate.setMonth(currentDate.getMonth() - 1);
            }
            updateCalendar();
            updateCurrentDate();
        }

        function nextPeriod() {
            if (currentView === 'week') {
                currentDate.setDate(currentDate.getDate() + 7);
            } else {
                currentDate.setMonth(currentDate.getMonth() + 1);
            }
            updateCalendar();
            updateCurrentDate();
        }

        // Actualizar fecha actual en el header
        function updateCurrentDate() {
            const dateElement = document.getElementById('currentDate');
            if (currentView === 'week') {
                const startWeek = getWeek

// Actualizar fecha actual en el header
function updateCurrentDate() {
    const dateElement = document.getElementById('currentDate');
    if (currentView === 'week') {
        const startWeek = getWeekStart(currentDate);
        const endWeek = new Date(startWeek);
        endWeek.setDate(startWeek.getDate() + 6);
        
        const startMonth = startWeek.toLocaleDateString('es-ES', { month: 'short' });
        const endMonth = endWeek.toLocaleDateString('es-ES', { month: 'short' });
        
        if (startMonth === endMonth) {
            dateElement.textContent = `${startWeek.getDate()} - ${endWeek.getDate()} ${startMonth} ${startWeek.getFullYear()}`;
        } else {
            dateElement.textContent = `${startWeek.getDate()} ${startMonth} - ${endWeek.getDate()} ${endMonth} ${startWeek.getFullYear()}`;
        }
    } else {
        dateElement.textContent = currentDate.toLocaleDateString('es-ES', { 
            month: 'long', 
            year: 'numeric' 
        });
    }
}

// Obtener el inicio de la semana
function getWeekStart(date) {
    const d = new Date(date);
    const day = d.getDay();
    const diff = d.getDate() - day;
    return new Date(d.setDate(diff));
}

// Actualizar fecha de hoy
function updateTodayDate() {
    const todayElement = document.getElementById('todayDate');
    const today = new Date();
    todayElement.textContent = today.toLocaleDateString('es-ES', { 
        weekday: 'long', 
        day: 'numeric', 
        month: 'long' 
    });
}

// Actualizar calendario
function updateCalendar() {
    if (currentView === 'week') {
        renderWeekView();
    } else {
        renderMonthView();
    }
}

// Renderizar vista semanal
function renderWeekView() {
    const weekStart = getWeekStart(currentDate);
    const cells = document.querySelectorAll('.day-cell');
    
    cells.forEach((cell, index) => {
        const cellDate = new Date(weekStart);
        cellDate.setDate(weekStart.getDate() + index);
        
        const dateStr = cellDate.toISOString().split('T')[0];
        cell.dataset.date = dateStr;
        
        // Limpiar contenido anterior
        cell.innerHTML = '';
        
        // Agregar tareas para esta fecha
        const dayTasks = tasks.filter(task => task.date === dateStr);
        dayTasks.forEach(task => {
            const taskElement = createTaskElement(task);
            cell.appendChild(taskElement);
        });
    });
}

// Renderizar vista mensual
function renderMonthView() {
    const monthView = document.getElementById('monthView');
    const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
    const startDate = getWeekStart(firstDay);
    
    // Limpiar vista anterior
    monthView.innerHTML = '';
    
    // Agregar headers de días
    const dayHeaders = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
    dayHeaders.forEach(day => {
        const header = document.createElement('div');
        header.className = 'day-header';
        header.textContent = day;
        monthView.appendChild(header);
    });
    
    // Agregar celdas de días
    const totalCells = 42; // 6 semanas x 7 días
    for (let i = 0; i < totalCells; i++) {
        const cellDate = new Date(startDate);
        cellDate.setDate(startDate.getDate() + i);
        
        const cell = document.createElement('div');
        cell.className = 'month-day';
        
        if (cellDate.getMonth() !== currentDate.getMonth()) {
            cell.classList.add('other-month');
        }
        
        if (isToday(cellDate)) {
            cell.classList.add('today');
        }
        
        const dateStr = cellDate.toISOString().split('T')[0];
        cell.dataset.date = dateStr;
        cell.onclick = () => openTaskModal(dateStr);
        
        // Número del día
        const dayNumber = document.createElement('div');
        dayNumber.className = 'month-day-number';
        dayNumber.textContent = cellDate.getDate();
        cell.appendChild(dayNumber);
        
        // Agregar tareas para esta fecha
        const dayTasks = tasks.filter(task => task.date === dateStr);
        dayTasks.forEach(task => {
            const taskElement = createTaskElement(task);
            cell.appendChild(taskElement);
        });
        
        monthView.appendChild(cell);
    }
}

// Crear elemento de tarea
function createTaskElement(task) {
    const taskElement = document.createElement('div');
    taskElement.className = `task ${task.priority}-priority`;
    taskElement.textContent = task.title;
    taskElement.onclick = (e) => {
        e.stopPropagation();
        editTask(task);
    };
    
    if (task.completed) {
        taskElement.classList.add('completed');
    }
    
    return taskElement;
}

// Verificar si es hoy
function isToday(date) {
    const today = new Date();
    return date.getDate() === today.getDate() &&
           date.getMonth() === today.getMonth() &&
           date.getFullYear() === today.getFullYear();
}

// Actualizar tareas de hoy
function updateTodayTasks() {
    const todayTasksContainer = document.getElementById('todayTasks');
    const today = new Date().toISOString().split('T')[0];
    const todayTasks = tasks.filter(task => task.date === today);
    
    todayTasksContainer.innerHTML = '';
    
    if (todayTasks.length === 0) {
        todayTasksContainer.innerHTML = '<div class="no-tasks">No hay tareas para hoy</div>';
        return;
    }
    
    todayTasks.forEach(task => {
        const taskElement = createTodayTaskElement(task);
        todayTasksContainer.appendChild(taskElement);
    });
}

// Crear elemento de tarea para la sección de hoy
function createTodayTaskElement(task) {
    const taskElement = document.createElement('div');
    taskElement.className = `today-task ${task.completed ? 'completed' : ''}`;
    
    taskElement.innerHTML = `
        <div class="task-checkbox ${task.completed ? 'completed' : ''}" 
             onclick="toggleTaskCompletion('${task.id}')"></div>
        <div class="task-info">
            <h3 class="task-title-today ${task.completed ? 'completed' : ''}">${task.title}</h3>
            <div class="task-meta">
                ${task.time ? `<div class="task-time">⏰ ${task.time}</div>` : ''}
                <div class="task-priority-badge ${task.priority}">
                    ${task.priority === 'high' ? '🔴' : task.priority === 'medium' ? '🟡' : '🔵'}
                    ${task.priority === 'high' ? 'Alta' : task.priority === 'medium' ? 'Media' : 'Baja'}
                </div>
            </div>
        </div>
        <div class="task-actions">
            <button class="task-action-btn" onclick="editTask(${JSON.stringify(task).replace(/"/g, '&quot;')})" title="Editar">
                ✏️
            </button>
            <button class="task-action-btn" onclick="deleteTask('${task.id}')" title="Eliminar">
                🗑️
            </button>
        </div>
    `;
    
    return taskElement;
}

// Alternar completado de tarea
async function toggleTaskCompletion(taskId) {
    const result = await toggleTaskCompletionOnServer(taskId);
    if (result.success) {
        loadTasks(); // Recargar tareas
        updateCalendar(); // Actualizar calendario
    } else {
        showAlert(result.message, 'error');
    }
}

// Abrir modal de tarea
function openTaskModal(date = null) {
    const modal = document.getElementById('taskModal');
    const form = document.getElementById('taskForm');
    const modalTitle = document.getElementById('modalTitle');
    const deleteBtn = document.getElementById('deleteBtn');
    
    // Limpiar formulario
    form.reset();
    editingTaskId = null;
    
    modalTitle.textContent = 'Nueva Tarea';
    deleteBtn.style.display = 'none';
    
    // Si se proporciona una fecha, establecerla
    if (date !== null) {
        const dateInput = document.getElementById('taskDate');
        if (typeof date === 'string') {
            dateInput.value = date;
        } else {
            const weekStart = getWeekStart(currentDate);
            const cellDate = new Date(weekStart);
            cellDate.setDate(weekStart.getDate() + date);
            dateInput.value = cellDate.toISOString().split('T')[0];
        }
    } else {
        // Establecer fecha actual por defecto
        const today = new Date();
        document.getElementById('taskDate').value = today.toISOString().split('T')[0];
    }
    
    modal.style.display = 'block';
}

// Cerrar modal
function closeTaskModal() {
    document.getElementById('taskModal').style.display = 'none';
    document.getElementById('alertContainer').innerHTML = '';
}

// Editar tarea
function editTask(task) {
    const modal = document.getElementById('taskModal');
    const modalTitle = document.getElementById('modalTitle');
    const deleteBtn = document.getElementById('deleteBtn');
    
    modalTitle.textContent = 'Editar Tarea';
    deleteBtn.style.display = 'block';
    
    // Llenar formulario con datos de la tarea
    document.getElementById('taskTitle').value = task.title;
    document.getElementById('taskDescription').value = task.description || '';
    document.getElementById('taskDate').value = task.date;
    document.getElementById('taskTime').value = task.time || '';
    document.getElementById('taskPriority').value = task.priority;
    
    editingTaskId = task.id;
    modal.style.display = 'block';
}

// Eliminar tarea
async function deleteTask(taskId = null) {
    const id = taskId || editingTaskId;
    if (!id) return;
    
    if (!confirm('¿Estás seguro de que quieres eliminar esta tarea?')) {
        return;
    }
    
    const result = await deleteTaskFromServer(id);
    if (result.success) {
        showAlert(result.message, 'success');
        loadTasks(); // Recargar tareas
        updateCalendar(); // Actualizar calendario
        closeTaskModal();
    } else {
        showAlert(result.message, 'error');
    }
}

// Manejar envío del formulario
document.getElementById('taskForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
        title: document.getElementById('taskTitle').value,
        description: document.getElementById('taskDescription').value,
        date: document.getElementById('taskDate').value,
        time: document.getElementById('taskTime').value,
        priority: document.getElementById('taskPriority').value
    };
    
    if (editingTaskId) {
        formData.id = editingTaskId;
    }
    
    const saveBtn = document.getElementById('saveBtn');
    saveBtn.disabled = true;
    saveBtn.textContent = 'Guardando...';
    
    const result = await saveTaskToServer(formData, !!editingTaskId);
    
    if (result.success) {
        showAlert(result.message, 'success');
        loadTasks(); // Recargar tareas
        updateCalendar(); // Actualizar calendario
        closeTaskModal();
    } else {
        showAlert(result.message, 'error');
    }
    
    saveBtn.disabled = false;
    saveBtn.textContent = 'Guardar';
});

// Cerrar modal al hacer clic fuera
window.addEventListener('click', function(e) {
    const modal = document.getElementById('taskModal');
    if (e.target === modal) {
        closeTaskModal();
    }
});

// Manejar tecla ESC para cerrar modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeTaskModal();
    }
});

// Inicializar cuando se carga la página
document.addEventListener('DOMContentLoaded', init);

// Actualizar tareas cada 30 segundos
setInterval(loadTasks, 30000);
    </script>
</body>
</html>
