/**
 * Coverage Assistant - Interactive Crawler
 * AJAX-based URL crawler with adaptive throttling
 *
 * @package STCWCoverageAssistant
 * @since 1.0.6
 */

(function($) {
    'use strict';
    
    /**
     * Adaptive Throttler Class
     * Dynamically adjusts delay between requests based on server performance
     */
    class AdaptiveThrottler {
        constructor() {
            this.minDelay = 500;       // Never faster than 500ms
            this.maxDelay = 3000;      // Never slower than 3s
            this.currentDelay = 1000;  // Start at 1 second
            this.responseTimes = [];   // Rolling window of last 10
            this.errorCount = 0;
            this.successCount = 0;
        }
        
        /**
         * Adjust delay after each request
         */
        adjust(responseTime, success) {
            // Track response times (rolling window)
            this.responseTimes.push(responseTime);
            if (this.responseTimes.length > 10) {
                this.responseTimes.shift();
            }
            
            // Track success/error
            if (success) {
                this.successCount++;
            } else {
                this.errorCount++;
            }
            
            const avgResponseTime = this.getAverage();
            const errorRate = this.getErrorRate();
            
            // Adjustment logic
            if (errorRate > 0.15) {
                // High error rate - slow down significantly
                this.currentDelay = Math.min(this.maxDelay, this.currentDelay * 1.5);
                console.log('⚠️ High error rate, slowing to', this.currentDelay, 'ms');
            }
            else if (avgResponseTime > 1500) {
                // Slow server - increase delay
                this.currentDelay = Math.min(this.maxDelay, this.currentDelay * 1.2);
                console.log('🐌 Slow server, increasing delay to', this.currentDelay, 'ms');
            }
            else if (avgResponseTime < 400 && errorRate < 0.05) {
                // Fast server - speed up
                this.currentDelay = Math.max(this.minDelay, this.currentDelay * 0.85);
                console.log('🚀 Fast server, decreasing delay to', this.currentDelay, 'ms');
            }
            
            return Math.round(this.currentDelay);
        }
        
        getAverage() {
            if (this.responseTimes.length === 0) return 500;
            return this.responseTimes.reduce((a, b) => a + b) / this.responseTimes.length;
        }
        
        getErrorRate() {
            const total = this.successCount + this.errorCount;
            return total > 0 ? this.errorCount / total : 0;
        }
        
        getCurrentDelay() {
            return Math.round(this.currentDelay);
        }
    }
    
    /**
     * Interactive Crawler Class
     */
    class InteractiveCrawler {
        constructor() {
            this.urlQueue = [];
            this.processed = 0;
            this.successful = 0;
            this.failed = 0;
            this.failedUrls = [];
            this.isRunning = false;
            this.isStopped = false;
            this.throttler = new AdaptiveThrottler();
            
            this.bindEvents();
        }
        
        /**
         * Bind UI events
         */
        bindEvents() {
            $('#stcwca-start-crawl').on('click', () => this.start());
            $('#stcwca-stop-crawl').on('click', () => this.stop());
            
            // Warn if user tries to close tab during crawl
            $(window).on('beforeunload', (e) => {
                if (this.isRunning && !this.isStopped) {
                    e.preventDefault();
                    return stcwcaCrawler.strings.confirmClose;
                }
            });
        }
        
        /**
         * Start the crawl
         */
        async start() {
            console.log('🚀 Starting crawler...');
            
            // Show loading state
            $('#stcwca-start-crawl').prop('disabled', true).text('Loading URLs...');
            
            try {
                // Get uncached URLs from server
                const response = await $.ajax({
                    url: stcwcaCrawler.ajaxUrl,
                    method: 'POST',
                    data: {
                        action: 'stcwca_get_uncached_urls',
                        nonce: stcwcaCrawler.nonce
                    }
                });
                
                if (!response.success) {
                    alert(response.data.message || 'Failed to get URLs');
                    $('#stcwca-start-crawl').prop('disabled', false).text('⚡ Start Crawling');
                    return;
                }
                
                this.urlQueue = response.data.urls;
                const totalUrls = response.data.count;
                
                if (totalUrls === 0) {
                    alert('No uncached URLs found!');
                    location.reload();
                    return;
                }
                
                // Initialize UI
                this.initializeUI(totalUrls);
                
                // Start processing
                this.isRunning = true;
                this.isStopped = false;
                await this.processQueue();
                
            } catch (error) {
                console.error('Error starting crawler:', error);
                alert('Failed to start crawler. Check console for details.');
                $('#stcwca-start-crawl').prop('disabled', false).text('⚡ Start Crawling');
            }
        }
        
        /**
         * Initialize progress UI
         */
        initializeUI(totalUrls) {
            $('#stcwca-crawl-initial').hide();
            $('#stcwca-crawl-progress').show();
            $('#stcwca-progress-total').text(totalUrls);
            $('#stcwca-progress-current').text(0);
            $('#stcwca-progress-percent').text(0);
            $('#stcwca-success').text(0);
            $('#stcwca-errors').text(0);
            $('#stcwca-speed').text(this.throttler.getCurrentDelay());
        }
        
        /**
         * Process the URL queue
         */
        async processQueue() {
            const totalUrls = this.urlQueue.length + this.processed;
            
            while (this.urlQueue.length > 0 && !this.isStopped) {
                const url = this.urlQueue.shift();
                
                try {
                    await this.processUrl(url);
                } catch (error) {
                    console.error('Error processing URL:', url, error);
                    this.failed++;
                    this.failedUrls.push({url, error: error.message});
                }
                
                this.processed++;
                this.updateProgress(totalUrls);
                
                // Adaptive delay before next request
                if (this.urlQueue.length > 0 && !this.isStopped) {
                    await this.delay(this.throttler.getCurrentDelay());
                }
            }
            
            // Complete
            if (!this.isStopped) {
                this.complete();
            }
        }
        
        /**
         * Process a single URL
         */
        async processUrl(url) {
            const startTime = Date.now();
            
            const response = await $.ajax({
                url: stcwcaCrawler.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'stcwca_process_url',
                    nonce: stcwcaCrawler.nonce,
                    url: url
                }
            });
            
            const responseTime = Date.now() - startTime;
            
            if (response.success && response.data.success) {
                this.successful++;
            } else {
                this.failed++;
                this.failedUrls.push({
                    url,
                    error: response.data.error || 'Unknown error'
                });
            }
            
            // Adjust throttling
            const newDelay = this.throttler.adjust(responseTime, response.success && response.data.success);
            $('#stcwca-speed').text(newDelay);
        }
        
        /**
         * Update progress UI
         */
        updateProgress(totalUrls) {
            const percent = Math.round((this.processed / totalUrls) * 100);
            
            $('#stcwca-progress-current').text(this.processed);
            $('#stcwca-progress-percent').text(percent);
            $('.stcwca-progress-fill').css('width', percent + '%');
            $('#stcwca-success').text(this.successful);
            $('#stcwca-errors').text(this.failed);
        }
        
        /**
         * Complete the crawl
         */
        complete() {
            this.isRunning = false;
            
            $('#stcwca-crawl-progress').hide();
            $('#stcwca-crawl-complete').show();
            $('#stcwca-final-success').text(this.successful);
            
            if (this.failed > 0) {
                $('#stcwca-final-errors').text(this.failed);
                $('#stcwca-final-errors-wrapper').show();
                
                console.log('Failed URLs:', this.failedUrls);
            }
            
            console.log('✅ Crawl complete!', {
                total: this.processed,
                successful: this.successful,
                failed: this.failed
            });
        }
        
        /**
         * Stop the crawl
         */
        stop() {
            if (confirm('Are you sure you want to stop crawling?')) {
                this.isStopped = true;
                this.isRunning = false;
                $('#stcwca-stop-crawl').prop('disabled', true).text('Stopping...');
                
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        }
        
        /**
         * Delay helper
         */
        delay(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }
    }
    
    // Initialize on document ready
    $(document).ready(function() {
        // Only initialize if crawler control exists
        if ($('#stcwca-crawl-control').length) {
            new InteractiveCrawler();
        }
    });
    
})(jQuery);
