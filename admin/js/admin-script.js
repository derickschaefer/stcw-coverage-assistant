/**
 * Coverage Analytics Admin JavaScript
 * Handles Copy Link functionality with modern UX
 *
 * @package STCWCoverageAnalytics
 * @since 1.0.0
 */

jQuery(document).ready(function($) {

    /**
     * Show toast notification
     * Modern, non-blocking notification similar to DigitalOcean's UX
     */
    function showToast(message, type) {
        type = type || 'success';
        
        // Remove any existing toasts
        $('.stcwca-toast').remove();
        
        // Create toast element
        var toast = $('<div class="stcwca-toast stcwca-toast-' + type + '">')
            .html('<span class="dashicons dashicons-' + (type === 'success' ? 'yes' : 'warning') + '"></span> ' + message)
            .appendTo('body');
        
        // Trigger animation
        setTimeout(function() {
            toast.addClass('stcwca-toast-show');
        }, 10);
        
        // Auto-hide after 2.5 seconds
        setTimeout(function() {
            toast.removeClass('stcwca-toast-show');
            setTimeout(function() {
                toast.remove();
            }, 300);
        }, 2500);
    }

    /**
     * Copy Link button click handler
     * Copies the URL of an uncached page to the clipboard with smooth feedback.
     */
    $(document).on('click', '.copy-link-button', function(e) {
        e.preventDefault();

        var $button = $(this);
        var url = $button.data('url');
        
        if (!url) {
            showToast('No URL found to copy', 'error');
            return;
        }

        // Store original button state
        var originalText = $button.text();
        var originalIcon = $button.find('.dashicons').attr('class');
        
        // Disable button during copy operation
        $button.prop('disabled', true).addClass('stcwca-copying');

        // Use the Clipboard API if available
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(function() {
                // Success feedback
                $button.html('<span class="dashicons dashicons-yes"></span> Copied!');
                showToast('Link copied to clipboard');
                
                // Reset button after 2 seconds
                setTimeout(function() {
                    $button.html(originalIcon ? '<span class="' + originalIcon + '"></span> ' + originalText : originalText)
                           .prop('disabled', false)
                           .removeClass('stcwca-copying');
                }, 2000);
            }).catch(function(err) {
                console.error('Clipboard error:', err);
                showToast('Failed to copy link', 'error');
                $button.prop('disabled', false).removeClass('stcwca-copying');
            });
        } else {
            // Fallback for very old browsers
            var tempInput = $('<input>');
            $('body').append(tempInput);
            tempInput.val(url).select();
            
            try {
                document.execCommand('copy');
                $button.html('<span class="dashicons dashicons-yes"></span> Copied!');
                showToast('Link copied to clipboard');
                
                setTimeout(function() {
                    $button.html(originalIcon ? '<span class="' + originalIcon + '"></span> ' + originalText : originalText)
                           .prop('disabled', false)
                           .removeClass('stcwca-copying');
                }, 2000);
            } catch (err) {
                showToast('Failed to copy link', 'error');
                console.error('Fallback copy error:', err);
                $button.prop('disabled', false).removeClass('stcwca-copying');
            }
            
            tempInput.remove();
        }
    });

});
