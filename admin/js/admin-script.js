/**
 * Coverage Analytics Admin JavaScript
 * Handles Copy Link functionality
 *
 * @package STCWCoverageAnalytics
 * @since 1.1.0
 */

jQuery(document).ready(function($) {

    /**
     * Copy Link button click handler
     * Copies the URL of an uncached page to the clipboard.
     */
    $(document).on('click', '.copy-link-button', function(e) {
        e.preventDefault();

        var url = $(this).data('url');
        if (!url) {
            alert('No URL found to copy.');
            return;
        }

        // Use the Clipboard API if available
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(function() {
                // Provide simple success feedback
                alert('Copied: ' + url);
            }).catch(function(err) {
                console.error('Clipboard error:', err);
                alert('Failed to copy link.');
            });
        } else {
            // Fallback for very old browsers
            var tempInput = $('<input>');
            $('body').append(tempInput);
            tempInput.val(url).select();
            try {
                document.execCommand('copy');
                alert('Copied: ' + url);
            } catch (err) {
                alert('Failed to copy link.');
                console.error('Fallback copy error:', err);
            }
            tempInput.remove();
        }
    });

});
