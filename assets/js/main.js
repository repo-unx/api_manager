/**
 * Main JavaScript file for API Manager
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const desktopToggleButton = document.getElementById('desktop-sidebar-toggle');
    const desktopCollapseButton = document.getElementById('desktop-sidebar-collapse');
    const sidebar = document.getElementById('sidebar');
    const sidebarBackdrop = document.getElementById('sidebar-backdrop');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const mainContent = document.getElementById('main-content');
    
    // Sidebar state
    let sidebarCollapsed = false;
    
    // Mobile sidebar toggle
    if (mobileMenuButton && sidebar && sidebarBackdrop) {
        mobileMenuButton.addEventListener('click', function() {
            sidebar.classList.toggle('-translate-x-full');
            sidebarBackdrop.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        });
        
        sidebarBackdrop.addEventListener('click', function() {
            sidebar.classList.add('-translate-x-full');
            sidebarBackdrop.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        });
    }
    
    // Desktop sidebar toggle
    if (desktopToggleButton && sidebar) {
        desktopToggleButton.addEventListener('click', function() {
            toggleSidebar();
        });
    }
    
    // Desktop sidebar collapse button
    if (desktopCollapseButton && sidebar) {
        desktopCollapseButton.addEventListener('click', function() {
            toggleSidebar();
        });
    }
    
    // Function to toggle sidebar on desktop
    function toggleSidebar() {
        if (window.innerWidth >= 768) { // md breakpoint
            sidebarCollapsed = !sidebarCollapsed;
            
            if (sidebarCollapsed) {
                sidebar.classList.add('-translate-x-full');
                mainContent.classList.remove('md:ml-64');
                sidebarOverlay.classList.add('hidden');
            } else {
                sidebar.classList.remove('-translate-x-full');
                mainContent.classList.add('md:ml-64');
                sidebarOverlay.classList.add('hidden');
            }
        }
    }
    
    // Close sidebar when clicking overlay
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            if (!sidebarCollapsed) {
                toggleSidebar();
            }
        });
    }
    
    // Alert close buttons
    const closeButtons = document.querySelectorAll('.close-alert');
    if (closeButtons) {
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const alert = this.closest('[role="alert"]');
                alert.classList.add('opacity-0');
                setTimeout(() => {
                    alert.remove();
                }, 300);
            });
        });
    }
    
    // Form validation
    const forms = document.querySelectorAll('form.needs-validation');
    if (forms) {
        forms.forEach(form => {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                form.classList.add('was-validated');
                
                // Add visual feedback for invalid fields
                const invalidFields = form.querySelectorAll(':invalid');
                invalidFields.forEach(field => {
                    field.classList.add('is-invalid');
                    
                    // Create feedback message if it doesn't exist
                    const parent = field.parentElement;
                    if (!parent.querySelector('.invalid-feedback')) {
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.textContent = field.validationMessage;
                        parent.appendChild(feedback);
                    }
                });
            }, false);
            
            // Remove is-invalid class when field value changes
            const formFields = form.querySelectorAll('input, select, textarea');
            formFields.forEach(field => {
                field.addEventListener('input', function() {
                    field.classList.remove('is-invalid');
                    const feedback = field.parentElement.querySelector('.invalid-feedback');
                    if (feedback) {
                        feedback.remove();
                    }
                });
            });
        });
    }
    
    // Initialize JSON Editors
    initializeJsonEditors();
    
    // Delete confirmations
    const deleteButtons = document.querySelectorAll('.delete-confirm');
    if (deleteButtons) {
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                    event.preventDefault();
                }
            });
        });
    }
    
    // Filter functionality
    const filterInput = document.getElementById('filter-input');
    if (filterInput) {
        filterInput.addEventListener('input', function() {
            const filterValue = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#data-table tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(filterValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});

/**
 * Initialize JSON Editors on the page
 */
function initializeJsonEditors() {
    const jsonEditors = {};
    const editorContainers = document.querySelectorAll('.json-editor');
    
    editorContainers.forEach(container => {
        const fieldName = container.dataset.field;
        const mode = container.dataset.mode || 'tree';
        const readOnly = container.dataset.readonly === 'true';
        const hiddenInput = document.getElementById(`${fieldName}-input`);
        
        // Create editor
        const editor = new JSONEditor(container, {
            mode: mode,
            modes: ['tree', 'code', 'form', 'text'],
            onChangeJSON: function(json) {
                if (hiddenInput) {
                    hiddenInput.value = JSON.stringify(json);
                }
            },
            onError: function(err) {
                console.error('JSON Editor error:', err);
            },
            search: true,
            statusBar: true,
            navigationBar: true,
            mainMenuBar: true,
            readOnly: readOnly
        });
        
        // Set initial value if present
        if (hiddenInput && hiddenInput.value) {
            try {
                const initialValue = JSON.parse(hiddenInput.value);
                editor.set(initialValue);
            } catch (e) {
                console.error('Error parsing JSON:', e);
                editor.setText(hiddenInput.value);
            }
        }
        
        // Store editor instance
        jsonEditors[fieldName] = editor;
    });
    
    // Make available globally
    window.jsonEditors = jsonEditors;
    
    return jsonEditors;
}

/**
 * Format JSON for better display
 */
function formatJson(jsonString) {
    try {
        const json = JSON.parse(jsonString);
        return JSON.stringify(json, null, 2);
    } catch (e) {
        return jsonString;
    }
}

/**
 * Toggle JSON viewer expanded/collapsed state
 */
function toggleJsonViewer(id) {
    const viewer = document.getElementById(id);
    if (viewer) {
        viewer.classList.toggle('hidden');
    }
}
