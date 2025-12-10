// Reservations page functionality
let confirmCallback = null;

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const preselectRoomId = urlParams.get('room_id');
    // Two-step form elements
    const step1Form = document.getElementById('reservation-form-step1');
    const step2Form = document.getElementById('reservation-form-step2');
    const studentIdsContainer = document.getElementById('student-ids-container');
    const step1Data = document.getElementById('step1-data');
    const nextBtnStep1 = document.getElementById('next-btn-step1');
    const cancelBtnStep1 = document.getElementById('cancel-btn-step1');
    const cancelBtnStep2 = document.getElementById('cancel-btn-step2');
    
    // Legacy form element (kept for compatibility)
    const form = document.getElementById('reservation-form');
    const roomSelect = document.getElementById('room');
    const dateInput = document.getElementById('date');
    const messageDiv = document.getElementById('form-message');
    const tableElement = document.getElementById('reservation-table');
    const loadingElement = document.getElementById('reservations-loading');
    const noDataElement = document.getElementById('no-reservations');

    // Modal elements
    const modal = document.getElementById('confirm-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalMessage = document.getElementById('modal-message');
    const modalConfirm = document.getElementById('modal-confirm');
    const modalCancel = document.getElementById('modal-cancel');

    // Modal handlers
    modalConfirm.addEventListener('click', function() {
        if (confirmCallback) {
            confirmCallback();
            confirmCallback = null;
        }
        closeModal();
    });

    modalCancel.addEventListener('click', function() {
        confirmCallback = null;
        closeModal();
    });

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            confirmCallback = null;
            closeModal();
        }
    });

    // ============ TWO-STEP FORM HANDLERS ============
    if (step1Form && step2Form && nextBtnStep1) {
        // Load rooms for step 1 (with optional preselect)
        loadRooms(preselectRoomId);
        
        // Next button: validate step 1 and transition to step 2
        nextBtnStep1.addEventListener('click', function(e) {
            e.preventDefault();
            if (!step1Form.checkValidity()) {
                step1Form.reportValidity();
                return;
            }
            
            const capacity = parseInt(document.getElementById('capacity').value);
            if (!capacity || capacity < 1) {
                alert('Please enter a valid number of students');
                return;
            }
            
            // Store step 1 data
            const step1Values = {
                email: document.getElementById('email').value,
                date: document.getElementById('date').value,
                room_id: document.getElementById('room').value,
                room_name: document.getElementById('room').options[document.getElementById('room').selectedIndex].text,
                purpose: document.getElementById('purpose').value,
                start_time: document.getElementById('start-time').value,
                end_time: document.getElementById('end-time').value,
                capacity: capacity
            };
            step1Data.dataset.values = JSON.stringify(step1Values);
            
            // Generate student ID fields for step 2
            generateStudentIdFields(capacity);
            
            // Transition to step 2
            step1Form.style.display = 'none';
            step2Form.style.display = 'grid';
        });
        
        // Cancel button on step 1
        cancelBtnStep1.addEventListener('click', function(e) {
            e.preventDefault();
            step1Form.reset();
            if (messageDiv) messageDiv.style.display = 'none';
        });
        
        // Cancel button on step 2
        cancelBtnStep2.addEventListener('click', function(e) {
            e.preventDefault();
            // Go back to step 1
            step1Form.style.display = 'grid';
            step2Form.style.display = 'none';
            studentIdsContainer.innerHTML = '';
            step1Data.dataset.values = '';
        });
        
        // Submit button on step 2
        step2Form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Collect student IDs
            const studentIds = [];
            const inputs = studentIdsContainer.querySelectorAll('input[name^="student_id_"]');
            inputs.forEach(input => {
                if (input.value.trim()) {
                    studentIds.push(input.value.trim());
                }
            });
            
            if (studentIds.length === 0) {
                showMessage('Please enter at least one student ID', 'error');
                return;
            }
            
            // Retrieve and merge step 1 data
            const step1Values = JSON.parse(step1Data.dataset.values || '{}');
            const formData = {
                room_id: parseInt(step1Values.room_id),
                reservation_date: step1Values.date,
                start_time: step1Values.start_time,
                end_time: step1Values.end_time,
                purpose: step1Values.purpose,
                student_ids: studentIds
            };
            
            // Show confirmation modal
            const timeStr = `${formatTime(formData.start_time)} - ${formatTime(formData.end_time)}`;
            showConfirmModal(
                'Confirm Reservation',
                `Reserve ${step1Values.room_name} on ${formatDate(formData.reservation_date)} from ${timeStr}? (${studentIds.length} student${studentIds.length > 1 ? 's' : ''})`,
                async function() {
                    await submitReservation(formData);
                }
            );
        });
    }

    function generateStudentIdFields(capacity) {
        studentIdsContainer.innerHTML = '';
        const heading = document.createElement('div');
        heading.style.cssText = 'margin-bottom: 0.75rem; font-weight: 600;';
        heading.textContent = `Enter ${capacity} Student ID(s)`;
        studentIdsContainer.appendChild(heading);
        
        for (let i = 1; i <= capacity; i++) {
            const div = document.createElement('div');
            div.style.cssText = 'display: grid; grid-template-columns: 150px 1fr; align-items: center; gap: 0.75rem;';
            
            const label = document.createElement('label');
            label.style.margin = '0';
            label.textContent = `Student ${i}`;
            
            const input = document.createElement('input');
            input.type = 'text';
            input.name = `student_id_${i}`;
            input.placeholder = `Student ${i} ID`;
            input.style.marginBottom = '0';
            input.required = true;
            
            div.appendChild(label);
            div.appendChild(input);
            studentIdsContainer.appendChild(div);
        }
    }

    function showConfirmModal(title, message, callback) {
        modalTitle.textContent = title;
        modalMessage.textContent = message;
        confirmCallback = callback;
        modal.classList.add('active');
    }

    function closeModal() {
        modal.classList.remove('active');
    }

    // Set minimum date to today
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);
    }

    // Load existing reservations
    loadReservations();

    // ============ LEGACY FORM HANDLERS (if old form is still used) ============
    // Handle cancel button for legacy form
    const cancelBtn = document.getElementById('cancel-btn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            if (form) {
                form.reset();
                if (messageDiv) {
                    messageDiv.style.display = 'none';
                }
            }
        });
    }

    // Handle legacy form submission
    if (form && !step1Form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = {
                room_id: parseInt(document.getElementById('room').value),
                reservation_date: document.getElementById('date').value,
                start_time: document.getElementById('start-time').value,
                end_time: document.getElementById('end-time').value,
                purpose: document.getElementById('purpose').value
            };

            // Show confirmation modal
            const roomName = roomSelect.options[roomSelect.selectedIndex].text;
            const dateStr = formatDate(formData.reservation_date);
            const timeStr = `${formatTime(formData.start_time)} - ${formatTime(formData.end_time)}`;
            
            showConfirmModal(
                'Confirm Reservation',
                `Are you sure you want to reserve ${roomName} on ${dateStr} from ${timeStr}?`,
                async function() {
                    await submitReservation(formData);
                }
            );
        });
    }

    async function submitReservation(formData) {
        try {
            const response = await fetch('../api/create_reservation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            let data;
            try {
                data = await response.json();
            } catch (err) {
                showMessage('Server error. Please try again.', 'error');
                return;
            }

            if (data.success) {
                showMessage(data.message, 'success');
                if (form) {
                    form.reset();
                } else {
                    if (step1Form) {
                        step1Form.reset();
                        step1Form.style.display = 'grid';
                    }
                    if (step2Form) {
                        step2Form.reset();
                        step2Form.style.display = 'none';
                    }
                    if (studentIdsContainer) studentIdsContainer.innerHTML = '';
                    if (step1Data) step1Data.dataset.values = '';
                }
                loadReservations(); // Refresh the list
            } else {
                const msg = data.message || 'Submission failed. Please check your inputs and try again.';
                showMessage(msg, 'error');
            }
        } catch (error) {
            showMessage('Error submitting reservation. Please try again.', 'error');
        }
    }

    async function loadRooms(selectedRoomId) {
        try {
            const response = await fetch('../api/get_rooms.php');
            const data = await response.json();

            if (data.success && data.rooms) {
                roomSelect.innerHTML = '<option value="">Select a room...</option>';
                data.rooms.forEach(room => {
                    const option = document.createElement('option');
                    option.value = room.id;
                    option.textContent = room.name;
                    if (room.status === 'maintenance') {
                        option.disabled = true;
                        option.textContent += ' - Under Maintenance';
                    }
                    roomSelect.appendChild(option);
                });

                // Preselect room if provided via query param and available
                if (selectedRoomId) {
                    roomSelect.value = selectedRoomId;
                }
            }
        } catch (error) {
        }
    }

    async function loadReservations() {
        try {
            const response = await fetch('../api/get_reservations.php');
            const data = await response.json();

            if (data.success && data.reservations) {
                displayReservations(data.reservations);
            }
        } catch (error) {
            loadingElement.textContent = 'Error loading reservations.';
        }
    }

    function displayReservations(reservations) {
        loadingElement.style.display = 'none';

        if (reservations.length === 0) {
            noDataElement.style.display = 'block';
            tableElement.style.display = 'none';
            return;
        }

        noDataElement.style.display = 'none';
        tableElement.style.display = 'table';

        const tbody = tableElement.querySelector('tbody');
        tbody.innerHTML = '';
        
        // Check if we're showing student column (librarian view) - only once
        const showStudent = tableElement.querySelector('thead th:first-child').textContent === 'Student';

        reservations.forEach(reservation => {
            const row = document.createElement('tr');
            
            if (showStudent) {
                const studentCell = document.createElement('td');
                studentCell.textContent = reservation.user_name || reservation.user_email;
                row.appendChild(studentCell);
            }

            const roomCell = document.createElement('td');
            roomCell.textContent = reservation.room_name;
            row.appendChild(roomCell);

            const dateCell = document.createElement('td');
            dateCell.textContent = formatDate(reservation.reservation_date);
            row.appendChild(dateCell);

            const timeCell = document.createElement('td');
            timeCell.textContent = `${formatTime(reservation.start_time)} - ${formatTime(reservation.end_time)}`;
            row.appendChild(timeCell);

            const purposeCell = document.createElement('td');
            purposeCell.textContent = reservation.purpose || 'N/A';
            purposeCell.style.maxWidth = '200px';
            purposeCell.style.overflow = 'hidden';
            purposeCell.style.textOverflow = 'ellipsis';
            purposeCell.style.whiteSpace = 'nowrap';
            row.appendChild(purposeCell);

            const statusCell = document.createElement('td');
            const statusBadge = document.createElement('span');
            statusBadge.className = `status-badge status-${reservation.status}`;
            statusBadge.textContent = reservation.status.charAt(0).toUpperCase() + reservation.status.slice(1);
            statusCell.appendChild(statusBadge);
            row.appendChild(statusCell);

            // Add cancel button for students on pending/approved reservations
            if (!showStudent && (reservation.status === 'pending' || reservation.status === 'approved')) {
                const actionCell = document.createElement('td');
                const cancelBtn = document.createElement('button');
                cancelBtn.className = 'btn';
                cancelBtn.style.cssText = 'padding: 0.4rem 0.8rem; font-size: 0.85rem; background: var(--color-primary); color: #fff; transition: background 0.2s;';
                cancelBtn.textContent = 'Cancel';
                cancelBtn.onmouseover = function() { this.style.background = 'var(--color-gold)'; };
                cancelBtn.onmouseout = function() { this.style.background = 'var(--color-primary)'; };
                cancelBtn.onclick = function() {
                    handleCancelReservation(reservation.id, reservation.room_name, reservation.reservation_date, reservation.start_time, reservation.end_time);
                };
                actionCell.appendChild(cancelBtn);
                row.appendChild(actionCell);
            } else if (!showStudent) {
                const actionCell = document.createElement('td');
                actionCell.textContent = '-';
                row.appendChild(actionCell);
            }

            tbody.appendChild(row);
        });
    }

    function handleCancelReservation(reservationId, roomName, date, startTime, endTime) {
        const dateStr = formatDate(date);
        const timeStr = `${formatTime(startTime)} - ${formatTime(endTime)}`;
        
        showConfirmModal(
            'Cancel Reservation',
            `Are you sure you want to cancel your reservation for ${roomName} on ${dateStr} from ${timeStr}?`,
            async function() {
                await cancelReservation(reservationId);
            }
        );
    }

    async function cancelReservation(reservationId) {
        try {
            const response = await fetch('../api/cancel_reservation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ reservation_id: reservationId })
            });

            const data = await response.json();

            if (data.success) {
                showMessage(data.message, 'success');
                loadReservations(); // Refresh the list
            } else {
                showMessage(data.message, 'error');
            }
        } catch (error) {
            showMessage('Error cancelling reservation. Please try again.', 'error');
        }
    }

    function showMessage(message, type) {
        if (!messageDiv) return;
        
        messageDiv.className = type === 'success' ? 'success-message' : 'error-message';
        messageDiv.textContent = message;
        messageDiv.style.display = 'block';

        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 5000);
    }

    function formatDate(dateString) {
        const date = new Date(dateString + 'T00:00:00');
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function formatTime(timeString) {
        const [hours, minutes] = timeString.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const hour12 = hour % 12 || 12;
        return `${hour12}:${minutes} ${ampm}`;
    }
});
