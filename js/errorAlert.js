/**
 * Displays a reusable error alert with a custom design using SweetAlert2.
 * @param {string} title - The title of the alert (optional, defaults to "Error").
 * @param {string} message - The error message to display.
 * @param {string} buttonText - The text for the confirm button (optional, defaults to "OK").
 * @param {Function} callback - Optional callback function to execute after clicking the button.
 */
function showErrorAlert(message, title = "Error", buttonText = "OK", callback = null) {
    Swal.fire({
        title: title,
        html: `<span class="error-message">${message}</span>`,
        icon: 'error',
        confirmButtonText: buttonText,
        showClass: {
            popup: 'error-animated-popup'
        },
        customClass: {
            popup: 'error-custom-popup',
            icon: 'error-custom-icon',
            title: 'error-custom-title',
            htmlContainer: 'error-custom-html',
            confirmButton: 'error-confirm-button'
        }
    }).then((result) => {
        if (result.isConfirmed && callback) {
            callback();
        }
    });
}

// Inject CSS styles for the custom error alert
const errorAlertStyles = document.createElement('style');
errorAlertStyles.textContent = `
    /* Base popup styling */
    .error-custom-popup {
        border-radius: 10px !important;
        background: #800000 !important;
        color: #ffffff !important;
        padding: 50px 24px 24px !important;
        max-width: 450px !important;
        width: calc(100% - 32px) !important;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.35) !important;
        position: relative !important;
        text-align: center !important;
        margin: 10px !important;
    }
    
    /* Animation for popup entrance */
    .error-animated-popup {
        animation: errorPopIn 0.3s ease-out !important;
    }
    
    @keyframes errorPopIn {
        0% {
            opacity: 0;
            transform: scale(0.9);
        }
        70% {
            opacity: 1;
            transform: scale(1.03);
        }
        100% {
            transform: scale(1);
        }
    }
    
    /* Error icon styling */
    .error-custom-icon {
        position: absolute !important;
        top: 0 !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        margin: 0 !important;
        border: 3px solid #ffffff !important;
        background-color: #800000 !important;
        width: 64px !important;
        height: 64px !important;
        border-radius: 50% !important;
        box-shadow: 0 0 0 6px #800000 !important;
    }
    
    .error-custom-icon.swal2-icon-show {
        animation: errorIconPulse 2s infinite ease-in-out !important;
    }
    
    @keyframes errorIconPulse {
        0% {
            box-shadow: 0 0 0 6px #800000, 0 0 0 0 rgba(255, 255, 255, 0.2);
        }
        70% {
            box-shadow: 0 0 0 6px #800000, 0 0 0 10px rgba(255, 255, 255, 0);
        }
        100% {
            box-shadow: 0 0 0 6px #800000, 0 0 0 0 rgba(255, 255, 255, 0);
        }
    }
    
    .error-custom-icon.swal2-error [class^=swal2-x-mark-line] {
        background-color: #ffffff !important;
        top: 50% !important;
        width: 28px !important;
        height: 3px !important;
    }
    
    /* Title styling */
    .error-custom-title {
        font-size: 22px !important;
        font-weight: 600 !important;
        color: #ffffff !important;
        margin-top: 8px !important;
        margin-bottom: 12px !important;
        display: none !important; /* Hide the title by default as per your example */
    }
    
    /* Show title when provided */
    .swal2-modal[class*='error-custom-popup']:has(.swal2-title:not(:empty)) .error-custom-title {
        display: block !important;
    }
    
    /* Message styling */
    .error-custom-html {
        margin-bottom: 16px !important;
    }
    
    .error-message {
        color: #ffffff !important;
        font-size: 18px !important;
        line-height: 1.4 !important;
        display: block !important;
        margin: 0 auto !important;
    }
    
    /* Button styling */
    .error-confirm-button {
        background-color: #ffffff !important;
        color: #800000 !important;
        border-radius: 5px !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        padding: 8px 24px !important;
        min-width: 80px !important;
        transition: all 0.2s !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
    }
    
    .error-confirm-button:hover, .error-confirm-button:focus {
        background-color: #f0f0f0 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 3px 5px rgba(0, 0, 0, 0.2) !important;
    }
    
    .error-confirm-button:active {
        transform: translateY(0) !important;
    }
    
    /* Responsive adjustments */
    @media (max-width: 480px) {
        .error-custom-popup {
            padding: 45px 16px 20px !important;
        }
        
        .error-message {
            font-size: 16px !important;
        }
        
        .error-custom-icon {
            width: 56px !important;
            height: 56px !important;
        }
        
        .error-custom-icon.swal2-error [class^=swal2-x-mark-line] {
            width: 24px !important;
        }
    }
`;
document.head.appendChild(errorAlertStyles);