// Registro-Diario JavaScript

// Tab functionality
function initTabs() {
    const tabs = document.querySelectorAll('.tab');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const targetId = tab.dataset.tab;
            
            // Remove active class from all tabs and contents
            tabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            tab.classList.add('active');
            document.getElementById(targetId).classList.add('active');
        });
    });
}

// Autocomplete functionality
// Key codes for keyboard navigation
const KEY_DOWN = 40;
const KEY_UP = 38;
const KEY_ENTER = 13;

function initAutocomplete(inputElement, suggestionsUrl, minChars = 2) {
    let currentFocus = -1;
    
    inputElement.addEventListener('input', async function() {
        const value = this.value;
        closeAllLists();
        
        if (!value || value.length < minChars) {
            return;
        }
        
        try {
            const response = await fetch(`${suggestionsUrl}?q=${encodeURIComponent(value)}`);
            const suggestions = await response.json();
            
            if (suggestions.length === 0) {
                return;
            }
            
            currentFocus = -1;
            const listDiv = document.createElement('div');
            listDiv.className = 'autocomplete-items';
            listDiv.id = this.id + '-autocomplete-list';
            
            this.parentNode.appendChild(listDiv);
            
            suggestions.forEach(suggestion => {
                const itemDiv = document.createElement('div');
                // Safely escape HTML to prevent XSS
                const escapedSuggestion = escapeHtml(suggestion);
                const escapedValue = escapeHtml(value);
                itemDiv.innerHTML = escapedSuggestion.replace(
                    new RegExp(escapedValue, 'gi'),
                    match => `<strong>${match}</strong>`
                );
                
                itemDiv.addEventListener('click', function() {
                    inputElement.value = suggestion;
                    closeAllLists();
                });
                
                listDiv.appendChild(itemDiv);
            });
        } catch (error) {
            console.error('Error fetching suggestions:', error);
        }
    });
    
    inputElement.addEventListener('keydown', function(e) {
        let list = document.getElementById(this.id + '-autocomplete-list');
        if (list) {
            let items = list.getElementsByTagName('div');
            
            if (e.keyCode === KEY_DOWN) {
                currentFocus++;
                addActive(items);
                e.preventDefault();
            } else if (e.keyCode === KEY_UP) {
                currentFocus--;
                addActive(items);
                e.preventDefault();
            } else if (e.keyCode === KEY_ENTER) {
                e.preventDefault();
                if (currentFocus > -1 && items[currentFocus]) {
                    items[currentFocus].click();
                }
            }
        }
    });
    
    function addActive(items) {
        if (!items) return false;
        removeActive(items);
        if (currentFocus >= items.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = items.length - 1;
        items[currentFocus].classList.add('autocomplete-active');
    }
    
    function removeActive(items) {
        for (let i = 0; i < items.length; i++) {
            items[i].classList.remove('autocomplete-active');
        }
    }
    
    function closeAllLists(element) {
        const items = document.getElementsByClassName('autocomplete-items');
        for (let i = 0; i < items.length; i++) {
            if (element !== items[i] && element !== inputElement) {
                items[i].parentNode.removeChild(items[i]);
            }
        }
    }
    
    document.addEventListener('click', function(e) {
        closeAllLists(e.target);
    });
}

// RUT validation and formatting
function validateRUT(rut) {
    // Remove dots and hyphens
    rut = rut.replace(/[^0-9kK]/g, '');
    
    if (rut.length < 2) {
        return false;
    }
    
    const verifier = rut.slice(-1).toUpperCase();
    const number = rut.slice(0, -1);
    
    let sum = 0;
    let multiplier = 2;
    
    for (let i = number.length - 1; i >= 0; i--) {
        sum += parseInt(number[i]) * multiplier;
        multiplier = multiplier < 7 ? multiplier + 1 : 2;
    }
    
    let expectedVerifier = 11 - (sum % 11);
    
    if (expectedVerifier === 11) {
        expectedVerifier = '0';
    } else if (expectedVerifier === 10) {
        expectedVerifier = 'K';
    } else {
        expectedVerifier = String(expectedVerifier);
    }
    
    return verifier === expectedVerifier;
}

function formatRUT(rut) {
    // Remove all non-numeric characters except K
    rut = rut.replace(/[^0-9kK]/g, '');
    
    if (rut.length < 2) {
        return rut;
    }
    
    const verifier = rut.slice(-1);
    let number = rut.slice(0, -1);
    
    // Add dots every 3 digits from right to left
    number = number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    
    return `${number}-${verifier}`;
}

function initRUTValidation(inputElement) {
    inputElement.addEventListener('blur', function() {
        const value = this.value.trim();
        
        if (value === '') {
            this.setCustomValidity('');
            return;
        }
        
        if (!validateRUT(value)) {
            this.setCustomValidity('RUT inválido');
            this.reportValidity();
        } else {
            this.setCustomValidity('');
            this.value = formatRUT(value);
        }
    });
    
    inputElement.addEventListener('input', function() {
        this.setCustomValidity('');
    });
}

// Form submission handler
async function submitForm(formElement, onSuccess) {
    formElement.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner"></span> Guardando...';
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showMessage('success', result.message || 'Registro guardado exitosamente');
                this.reset();
                
                if (onSuccess) {
                    onSuccess(result);
                }
            } else {
                showMessage('error', result.message || 'Error al guardar el registro');
            }
        } catch (error) {
            console.error('Error submitting form:', error);
            showMessage('error', 'Error de conexión. Por favor, intente nuevamente.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
}

// Show message
function showMessage(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'error'}`;
    alertDiv.textContent = message;
    
    const container = document.querySelector('.container');
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Update exit time
async function updateExitTime(recordId, exitTime) {
    try {
        const formData = new FormData();
        formData.append('record_id', recordId);
        formData.append('exit_time', exitTime);
        
        const response = await fetch('/app/update_exit_time.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage('success', 'Hora de salida actualizada');
            // Refresh pending records
            loadPendingRecords();
        } else {
            showMessage('error', result.message || 'Error al actualizar');
        }
    } catch (error) {
        console.error('Error updating exit time:', error);
        showMessage('error', 'Error de conexión');
    }
}

// Utility function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Load pending records
async function loadPendingRecords() {
    try {
        const response = await fetch('/app/get_pending_records.php');
        const records = await response.json();
        
        const tbody = document.querySelector('#pending-records-table tbody');
        tbody.innerHTML = '';
        
        if (records.length === 0) {
            tbody.innerHTML = '<tr><td colspan="10" class="text-center">No hay registros pendientes</td></tr>';
            return;
        }
        
        records.forEach(record => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${escapeHtml(record.date)}</td>
                <td>${escapeHtml(record.name)}</td>
                <td>${escapeHtml(record.rut)}</td>
                <td>${escapeHtml(record.company_name || '-')}</td>
                <td>${escapeHtml(record.classification)}</td>
                <td>${escapeHtml(record.entry_time)}</td>
                <td>
                    <input type="time" class="exit-time-input" data-record-id="${escapeHtml(record.id)}" value="">
                </td>
                <td>${escapeHtml(record.license_plate || '-')}</td>
                <td><span class="badge badge-pending">Pendiente</span></td>
                <td>
                    <button class="btn btn-success btn-sm update-exit-btn" data-record-id="${escapeHtml(record.id)}">
                        Actualizar
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
        
        // Add event listeners to update buttons
        document.querySelectorAll('.update-exit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const recordId = this.getAttribute('data-record-id');
                updateExitTimeForRecord(recordId);
            });
        });
    } catch (error) {
        console.error('Error loading pending records:', error);
    }
}

function updateExitTimeForRecord(recordId) {
    const input = document.querySelector(`input[data-record-id="${recordId}"]`);
    const exitTime = input.value;
    
    if (!exitTime) {
        showMessage('error', 'Por favor ingrese la hora de salida');
        return;
    }
    
    updateExitTime(recordId, exitTime);
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tabs
    initTabs();
    
    // Initialize autocomplete for name field
    const nameInput = document.getElementById('name');
    if (nameInput) {
        initAutocomplete(nameInput, '/app/autocomplete.php?type=name');
    }
    
    // Initialize autocomplete for company field
    const companyInput = document.getElementById('company_name');
    if (companyInput) {
        initAutocomplete(companyInput, '/app/autocomplete.php?type=company');
    }
    
    // Initialize RUT validation
    const rutInput = document.getElementById('rut');
    if (rutInput) {
        initRUTValidation(rutInput);
    }
    
    // Set current date as default
    const dateInput = document.getElementById('date');
    if (dateInput && !dateInput.value) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.value = today;
    }
    
    // Initialize form submission
    const recordForm = document.getElementById('record-form');
    if (recordForm) {
        submitForm(recordForm, function() {
            // Reload pending records after successful submission
            loadPendingRecords();
        });
    }
    
    // Load pending records on pending tab
    const pendingTab = document.querySelector('[data-tab="pending"]');
    if (pendingTab) {
        pendingTab.addEventListener('click', function() {
            loadPendingRecords();
        });
    }
    
    // Load pending records initially if on pending tab
    if (document.getElementById('pending').classList.contains('active')) {
        loadPendingRecords();
    }
});
