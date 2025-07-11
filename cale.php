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
            updateTodayTasks();
            updateTodayDate();
        }

        // Cargar tareas desde memoria
        function loadTasks() {
            // Algunas tareas de ejemplo
            tasks = [
                {
                    id: 1,
                    title: "Reunión de equipo",
                    description: "Reunión semanal con el equipo",
                    date: formatDate(new Date()),
                    time: "09:00",
                    priority: "high",
                    completed: false
                },
                {
                    id: 2,
                    title: "Revisar propuesta",
                    description: "Revisar la propuesta del cliente",
                    date: formatDate(new Date(Date.now() + 24 * 60 * 60 * 1000)),
                    time: "14:30",
                    priority: "medium",
                    completed: false
                }
            ];
        }

        // Guardar tareas en memoria
        function saveTasks() {
            // En una aplicación real, aquí guardarías en una base de datos
            console.log('Tareas guardadas:', tasks);
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
                const startWeek = getWeekStart(currentDate);
                const endWeek = new Date(startWeek);
                endWeek.setDate(endWeek.getDate() + 6);
                dateElement.textContent = `${formatDateDisplay(startWeek)} - ${formatDateDisplay(endWeek)}`;
            } else {
                dateElement.textContent = currentDate.toLocaleDateString('es-ES', { 
                    month: 'long', 
                    year: 'numeric' 
                });
            }
        }

        // Obtener inicio de semana
        function getWeekStart(date) {
            const d = new Date(date);
            const day = d.getDay();
            const diff = d.getDate() - day;
            return new Date(d.setDate(diff));
        }

        // Formatear fecha
        function formatDate(date) {
            return date.toISOString().split('T')[0];
        }

        function formatDateDisplay(date) {
            return date.toLocaleDateString('es-ES', { 
                day: 'numeric', 
                month: 'short' 
            });
        }

        // Actualizar calendario
        function updateCalendar() {
            if (currentView === 'week') {
                renderWeekView();
            } else {
                renderMonthView();
            }
            updateTodayTasks();
        }

        // Renderizar vista semanal
        function renderWeekView() {
            const weekStart = getWeekStart(currentDate);
            const dayCells = document.querySelectorAll('.day-cell');
            
            dayCells.forEach((cell, index) => {
                const cellDate = new Date(weekStart);
                cellDate.setDate(cellDate.getDate() + index);
                
                cell.innerHTML = '';
                cell.setAttribute('data-date', formatDate(cellDate));
                
                // Agregar número del día
                const dayNumber = document.createElement('div');
                dayNumber.style.fontSize = '12px';
                dayNumber.style.fontWeight = '600';
                dayNumber.style.marginBottom = '4px';
                dayNumber.style.color = '#787774';
                dayNumber.textContent = cellDate.getDate();
                cell.appendChild(dayNumber);
                
                // Agregar tareas del día
                const dayTasks = tasks.filter(task => task.date === formatDate(cellDate));
                dayTasks.forEach(task => {
                    const taskElement = createTaskElement(task);
                    cell.appendChild(taskElement);
                });
            });
        }

        // Renderizar vista mensual
        function renderMonthView() {
            const monthView = document.getElementById('monthView');
            monthView.innerHTML = '';
            
            // Headers de días
            const dayHeaders = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
            dayHeaders.forEach(day => {
                const header = document.createElement('div');
                header.className = 'day-header';
                header.textContent = day;
                monthView.appendChild(header);
            });
            
            // Días del mes
            const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());
            
            for (let i = 0; i < 42; i++) {
                const cellDate = new Date(startDate);
                cellDate.setDate(cellDate.getDate() + i);
                
                const dayCell = document.createElement('div');
                dayCell.className = 'month-day';
                dayCell.setAttribute('data-date', formatDate(cellDate));
                dayCell.onclick = () => openTaskModal(formatDate(cellDate));
                
                if (cellDate.getMonth() !== currentDate.getMonth()) {
                    dayCell.classList.add('other-month');
                }
                
                if (formatDate(cellDate) === formatDate(new Date())) {
                    dayCell.classList.add('today');
                }
                
                const dayNumber = document.createElement('div');
                dayNumber.className = 'month-day-number';
                dayNumber.textContent = cellDate.getDate();
                dayCell.appendChild(dayNumber);
                
                // Agregar tareas
                const dayTasks = tasks.filter(task => task.date === formatDate(cellDate));
                dayTasks.forEach(task => {
                    const taskElement = createTaskElement(task);
                    dayCell.appendChild(taskElement);
                });
                
                monthView.appendChild(dayCell);
            }
        }

        // Crear elemento de tarea
        function createTaskElement(task) {
            const taskElement = document.createElement('div');
            taskElement.className = `task ${task.priority}-priority`;
            if (task.completed) {
                taskElement.classList.add('completed');
            }
            taskElement.textContent = task.title;
            taskElement.onclick = (e) => {
                e.stopPropagation();
                editTask(task.id);
            };
            return taskElement;
        }

        // Abrir modal de tarea
        function openTaskModal(date = null) {
            const modal = document.getElementById('taskModal');
            const form = document.getElementById('taskForm');
            
            // Resetear formulario
            form.reset();
            editingTaskId = null;
            
            document.getElementById('modalTitle').textContent = 'Nueva Tarea';
            document.getElementById('deleteBtn').style.display = 'none';
            
            // Si se proporciona una fecha, usarla
            if (date !== null) {
                if (typeof date === 'string') {
                    document.getElementById('taskDate').value = date;
                } else {
                    const weekStart = getWeekStart(currentDate);
                    const cellDate = new Date(weekStart);
                    cellDate.setDate(cellDate.getDate() + date);
                    document.getElementById('taskDate').value = formatDate(cellDate);
                }
            } else {
                document.getElementById('taskDate').value = formatDate(new Date());
            }
            
            modal.style.display = 'block';
        }

        // Cerrar modal
        function closeTaskModal() {
            document.getElementById('taskModal').style.display = 'none';
        }

        // Editar tarea
        function editTask(taskId) {
            const task = tasks.find(t => t.id === taskId);
            if (!task) return;
            
            editingTaskId = taskId;
            
            document.getElementById('modalTitle').textContent = 'Editar Tarea';
            document.getElementById('taskTitle').value = task.title;
            document.getElementById('taskDescription').value = task.description;
            document.getElementById('taskDate').value = task.date;
            document.getElementById('taskTime').value = task.time;
            document.getElementById('taskPriority').value = task.priority;
            document.getElementById('deleteBtn').style.display = 'inline-block';
            
            document.getElementById('taskModal').style.display = 'block';
        }

        // Guardar tarea
        function saveTask() {
            const title = document.getElementById('taskTitle').value;
            const description = document.getElementById('taskDescription').value;
            const date = document.getElementById('taskDate').value;
            const time = document.getElementById('taskTime').value;
            const priority = document.getElementById('taskPriority').value;
            
            if (!title || !date) {
                alert('Por favor completa los campos obligatorios');
                return;
            }
            
            if (editingTaskId) {
                // Editar tarea existente
                const taskIndex = tasks.findIndex(t => t.id === editingTaskId);
                tasks[taskIndex] = {
                    ...tasks[taskIndex],
                    title,
                    description,
                    date,
                    time,
                    priority
                };
            } else {
                // Nueva tarea
                const newTask = {
                    id: Date.now(),
                    title,
                    description,
                    date,
                    time,
                    priority,
                    completed: false
                };
                tasks.push(newTask);
            }
            
            saveTasks();
            updateCalendar();
            closeTaskModal();
        }

        // Eliminar tarea
        function deleteTask() {
            if (editingTaskId && confirm('¿Estás seguro de que quieres eliminar esta tarea?')) {
                tasks = tasks.filter(t => t.id !== editingTaskId);
                saveTasks();
                updateCalendar();
                closeTaskModal();
            }
        }

        // Actualizar fecha de hoy
        function updateTodayDate() {
            const today = new Date();
            const todayDateElement = document.getElementById('todayDate');
            todayDateElement.textContent = today.toLocaleDateString('es-ES', {
                weekday: 'long',
                day: 'numeric',
                month: 'long'
            });
        }

        // Actualizar tareas de hoy
        function updateTodayTasks() {
            const today = formatDate(new Date());
            const todayTasks = tasks.filter(task => task.date === today);
            const todayTasksContainer = document.getElementById('todayTasks');
            
            todayTasksContainer.innerHTML = '';
            
            if (todayTasks.length === 0) {
                todayTasksContainer.innerHTML = '<div class="no-tasks">No tienes tareas pendientes para hoy 🎉</div>';
                return;
            }
            
            // Ordenar por hora
            todayTasks.sort((a, b) => {
                if (!a.time) return 1;
                if (!b.time) return -1;
                return a.time.localeCompare(b.time);
            });
            
            todayTasks.forEach(task => {
                const taskElement = createTodayTaskElement(task);
                todayTasksContainer.appendChild(taskElement);
            });
        }

        // Crear elemento de tarea para la sección de hoy
        function createTodayTaskElement(task) {
            const taskElement = document.createElement('div');
            taskElement.className = `today-task ${task.completed ? 'completed' : ''}`;
            
            const checkbox = document.createElement('div');
            checkbox.className = `task-checkbox ${task.completed ? 'completed' : ''}`;
            checkbox.onclick = () => toggleTaskCompletion(task.id);
            
            const taskInfo = document.createElement('div');
            taskInfo.className = 'task-info';
            
            const taskTitle = document.createElement('div');
            taskTitle.className = `task-title-today ${task.completed ? 'completed' : ''}`;
            taskTitle.textContent = task.title;
            
            const taskMeta = document.createElement('div');
            taskMeta.className = 'task-meta';
            
            if (task.time) {
                const timeSpan = document.createElement('span');
                timeSpan.className = 'task-time';
                timeSpan.innerHTML = `🕐 ${task.time}`;
                taskMeta.appendChild(timeSpan);
            }
            
            const priorityBadge = document.createElement('span');
            priorityBadge.className = `task-priority-badge ${task.priority}`;
            const priorityText = {
                high: 'Alta',
                medium: 'Media',
                low: 'Baja'
            };
            priorityBadge.textContent = priorityText[task.priority];
            taskMeta.appendChild(priorityBadge);
            
            taskInfo.appendChild(taskTitle);
            if (task.description) {
                const description = document.createElement('div');
                description.style.fontSize = '14px';
                description.style.color = '#787774';
                description.textContent = task.description;
                taskInfo.appendChild(description);
            }
            taskInfo.appendChild(taskMeta);
            
            const taskActions = document.createElement('div');
            taskActions.className = 'task-actions';
            
            const editBtn = document.createElement('button');
            editBtn.className = 'task-action-btn';
            editBtn.innerHTML = '✏️';
            editBtn.title = 'Editar tarea';
            editBtn.onclick = (e) => {
                e.stopPropagation();
                editTask(task.id);
            };
            
            taskActions.appendChild(editBtn);
            
            taskElement.appendChild(checkbox);
            taskElement.appendChild(taskInfo);
            taskElement.appendChild(taskActions);
            
            return taskElement;
        }

        // Alternar completado de tarea
        function toggleTaskCompletion(taskId) {
            const task = tasks.find(t => t.id === taskId);
            if (task) {
                task.completed = !task.completed;
                saveTasks();
                updateCalendar();
                updateTodayTasks();
            }
        }

        // Event listeners
        document.getElementById('taskForm').addEventListener('submit', (e) => {
            e.preventDefault();
            saveTask();
        });

        // Cerrar modal al hacer clic fuera
        window.onclick = (event) => {
            const modal = document.getElementById('taskModal');
            if (event.target === modal) {
                closeTaskModal();
            }
        };

        // Inicializar
        init();
    </script>
</body>
</html>
