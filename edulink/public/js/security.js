// Security Protection for Student Portal
(function() {
    'use strict';
    
    // Disable right-click context menu
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        return false;
    });
    
    // Disable keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // F12 - Developer Tools
        if (e.key === 'F12') {
            e.preventDefault();
            return false;
        }
        
        // Ctrl+Shift+I - Developer Tools
        if (e.ctrlKey && e.shiftKey && e.key === 'I') {
            e.preventDefault();
            return false;
        }
        
        // Ctrl+U - View Source
        if (e.ctrlKey && e.key === 'u') {
            e.preventDefault();
            return false;
        }
        
        // Ctrl+S - Save Page
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            return false;
        }
        
        // Ctrl+Shift+C - Element Inspector
        if (e.ctrlKey && e.shiftKey && e.key === 'C') {
            e.preventDefault();
            return false;
        }
        
        // Ctrl+Shift+J - Console
        if (e.ctrlKey && e.shiftKey && e.key === 'J') {
            e.preventDefault();
            return false;
        }
    });
    
    // Disable text selection
    document.onselectstart = function() {
        return false;
    };
    
    // Disable drag and drop
    document.ondragstart = function() {
        return false;
    };
    
    // Clear console periodically
    setInterval(function() {
        console.clear();
    }, 1000);
    
    // DevTools detection
    let devtools = {
        open: false,
        orientation: null
    };
    
    const threshold = 160;
    
    setInterval(function() {
        if (window.outerHeight - window.innerHeight > threshold || 
            window.outerWidth - window.innerWidth > threshold) {
            if (!devtools.open) {
                devtools.open = true;
                // Redirect based on current page context
                if (window.location.pathname.includes('/admin')) {
                    window.location.href = '/admin/login';
                } else if (window.location.pathname.includes('/student')) {
                    window.location.href = '/student/dashboard';
                } else {
                    // For login pages, redirect to home or refresh
                    window.location.reload();
                }
            }
        } else {
            devtools.open = false;
        }
    }, 500);
    
    // Disable print
    window.addEventListener('beforeprint', function(e) {
        e.preventDefault();
        return false;
    });
    
    // Disable Ctrl+P
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            return false;
        }
    });
    
})();