/**
 * Confirmation Modal Component
 * Replaces native JavaScript alert() and confirm() with Bootstrap modals
 */

// Global confirmation modal functionality
window.confirmModal = {
    // Show confirmation modal
    confirm: function(message, callback, options = {}) {
        const modal = $('#confirmationModal');
        const messageElement = $('#confirmationMessage');
        const confirmButton = $('#confirmButton');
        const title = options.title || 'Confirm Action';
        const confirmText = options.confirmText || 'Confirm';
        const confirmClass = options.confirmClass || 'btn-primary';
        
        // Set modal content
        $('#confirmationModalLabel').text(title);
        messageElement.text(message);
        confirmButton.text(confirmText);
        confirmButton.removeClass('btn-primary btn-danger btn-warning btn-success').addClass(confirmClass);
        
        // Remove any existing click handlers
        confirmButton.off('click.confirmModal');
        
        // Add click handler for confirm button
        confirmButton.on('click.confirmModal', function() {
            modal.modal('hide');
            if (typeof callback === 'function') {
                callback(true);
            }
        });
        
        // Handle modal hidden event (user clicked cancel or closed modal)
        modal.off('hidden.bs.modal.confirmModal').on('hidden.bs.modal.confirmModal', function() {
            if (typeof callback === 'function') {
                // Only call callback with false if the confirm button wasn't clicked
                const wasConfirmed = confirmButton.data('confirmed');
                if (!wasConfirmed && typeof callback === 'function') {
                    callback(false);
                }
                confirmButton.removeData('confirmed');
            }
        });
        
        // Mark as confirmed when confirm button is clicked
        confirmButton.on('click.confirmModal', function() {
            confirmButton.data('confirmed', true);
        });
        
        // Show the modal
        modal.modal('show');
    },
    
    // Show alert modal (confirmation modal with just an OK button)
    alert: function(message, callback, options = {}) {
        const modal = $('#confirmationModal');
        const messageElement = $('#confirmationMessage');
        const confirmButton = $('#confirmButton');
        const cancelButton = modal.find('[data-dismiss="modal"]');
        const title = options.title || 'Alert';
        const okText = options.okText || 'OK';
        
        // Set modal content
        $('#confirmationModalLabel').text(title);
        messageElement.text(message);
        confirmButton.text(okText);
        confirmButton.removeClass('btn-primary btn-danger btn-warning btn-success').addClass('btn-primary');
        
        // Hide cancel button for alerts
        cancelButton.hide();
        
        // Remove any existing click handlers
        confirmButton.off('click.alertModal');
        
        // Add click handler for OK button
        confirmButton.on('click.alertModal', function() {
            modal.modal('hide');
            if (typeof callback === 'function') {
                callback();
            }
        });
        
        // Show cancel button again when modal is hidden
        modal.off('hidden.bs.modal.alertModal').on('hidden.bs.modal.alertModal', function() {
            cancelButton.show();
        });
        
        // Show the modal
        modal.modal('show');
    }
};

// Override native confirm and alert functions (optional)
window.originalConfirm = window.confirm;
window.originalAlert = window.alert;

// Replace native confirm with modal version
window.confirm = function(message) {
    return new Promise((resolve) => {
        window.confirmModal.confirm(message, resolve);
    });
};

// Replace native alert with modal version  
window.alert = function(message) {
    return new Promise((resolve) => {
        window.confirmModal.alert(message, resolve);
    });
};

// Utility function for common confirmation patterns
window.confirmAction = function(message, onConfirm, onCancel, options = {}) {
    window.confirmModal.confirm(message, function(confirmed) {
        if (confirmed && typeof onConfirm === 'function') {
            onConfirm();
        } else if (!confirmed && typeof onCancel === 'function') {
            onCancel();
        }
    }, options);
};

// Utility function for delete confirmations
window.confirmDelete = function(itemName, onConfirm, onCancel) {
    const message = itemName 
        ? `Are you sure you want to delete "${itemName}"? This action cannot be undone.`
        : 'Are you sure you want to delete this item? This action cannot be undone.';
    
    window.confirmAction(message, onConfirm, onCancel, {
        title: 'Confirm Delete',
        confirmText: 'Delete',
        confirmClass: 'btn-danger'
    });
};

// Utility function for action confirmations with custom styling
window.confirmStatus = function(action, itemName, onConfirm, onCancel) {
    const message = `Are you sure you want to ${action.toLowerCase()} ${itemName || 'this item'}?`;
    
    let confirmClass = 'btn-primary';
    if (action.toLowerCase().includes('delete') || action.toLowerCase().includes('remove')) {
        confirmClass = 'btn-danger';
    } else if (action.toLowerCase().includes('suspend') || action.toLowerCase().includes('reject')) {
        confirmClass = 'btn-warning';
    } else if (action.toLowerCase().includes('approve') || action.toLowerCase().includes('activate')) {
        confirmClass = 'btn-success';
    }
    
    window.confirmAction(message, onConfirm, onCancel, {
        title: `Confirm ${action}`,
        confirmText: action,
        confirmClass: confirmClass
    });
};