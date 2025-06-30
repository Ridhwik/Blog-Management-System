

document.addEventListener('DOMContentLoaded', function() {
    
    
    const links = document.querySelectorAll('a[href^="#"]');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Auto-hide success/error messages after 5 seconds
    const messages = document.querySelectorAll('.success, .error');
    messages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            message.style.transition = 'opacity 0.5s';
            setTimeout(() => {
                message.style.display = 'none';
            }, 500);
        }, 5000);
    });

    // Confirm delete actions
    const deleteLinks = document.querySelectorAll('a[href*="delete"], .delete');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });

    
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        
        autoResize(textarea);
        
       
        textarea.addEventListener('input', function() {
            autoResize(this);
        });
    });

    function autoResize(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    }

    
    const titleInput = document.querySelector('input[name="title"]');
    const excerptTextarea = document.querySelector('textarea[name="excerpt"]');

    if (titleInput) {
        addCharacterCounter(titleInput, 255, 'Title');
    }

    if (excerptTextarea) {
        addCharacterCounter(excerptTextarea, 500, 'Excerpt');
    }

    function addCharacterCounter(element, maxLength, label) {
        const counter = document.createElement('div');
        counter.className = 'char-counter';
        counter.style.cssText = 'font-size: 0.875rem; color: #666; margin-top: 0.25rem; text-align: right;';
        
        element.parentNode.insertBefore(counter, element.nextSibling);
        
        function updateCounter() {
            const length = element.value.length;
            counter.textContent = `${length}/${maxLength} characters`;
            
            if (length > maxLength * 0.9) {
                counter.style.color = '#e74c3c';
            } else if (length > maxLength * 0.7) {
                counter.style.color = '#f39c12';
            } else {
                counter.style.color = '#666';
            }
        }
        
        updateCounter();
        element.addEventListener('input', updateCounter);
    }

    // Simple content preview (for admin)
    const contentTextarea = document.querySelector('textarea[name="content"]');
    if (contentTextarea && window.location.pathname.includes('admin')) {
        addPreviewButton(contentTextarea);
    }

    function addPreviewButton(textarea) {
        const previewBtn = document.createElement('button');
        previewBtn.type = 'button';
        previewBtn.textContent = 'Preview';
        previewBtn.className = 'btn-secondary';
        previewBtn.style.marginLeft = '1rem';
        
        const previewDiv = document.createElement('div');
        previewDiv.className = 'content-preview';
        previewDiv.style.cssText = `
            display: none;
            margin-top: 1rem;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #f9f9f9;
            max-height: 300px;
            overflow-y: auto;
        `;
        
       
        const submitBtn = document.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.parentNode.insertBefore(previewBtn, submitBtn);
        }
        
        
        textarea.parentNode.insertBefore(previewDiv, textarea.nextSibling);
        
        let isPreviewVisible = false;
        
        previewBtn.addEventListener('click', function() {
            if (!isPreviewVisible) {
                previewDiv.innerHTML = textarea.value.replace(/\n/g, '<br>');
                previewDiv.style.display = 'block';
                previewBtn.textContent = 'Hide Preview';
                isPreviewVisible = true;
            } else {
                previewDiv.style.display = 'none';
                previewBtn.textContent = 'Preview';
                isPreviewVisible = false;
            }
        });
    }

    
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#e74c3c';
                    isValid = false;
                } else {
                    field.style.borderColor = '#ddd';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });

    
    const submitButtons = document.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            if (form.checkValidity()) {
                this.textContent = 'Saving...';
                this.disabled = true;
                
                
                setTimeout(() => {
                    this.disabled = false;
                    this.textContent = this.textContent.replace('Saving...', '');
                }, 10000);
            }
        });
    });

   
    const searchInput = document.querySelector('#blog-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const posts = document.querySelectorAll('.blog-post');
            
            posts.forEach(post => {
                const title = post.querySelector('h2').textContent.toLowerCase();
                const excerpt = post.querySelector('.post-excerpt').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || excerpt.includes(searchTerm)) {
                    post.style.display = 'block';
                } else {
                    post.style.display = 'none';
                }
            });
        });
    }

    
    const backToTopBtn = document.createElement('button');
    backToTopBtn.innerHTML = '↑';
    backToTopBtn.className = 'back-to-top';
    backToTopBtn.style.cssText = `
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #3498db;
        color: white;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        display: none;
        z-index: 1000;
        transition: all 0.3s;
    `;
    
    document.body.appendChild(backToTopBtn);
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            backToTopBtn.style.display = 'block';
        } else {
            backToTopBtn.style.display = 'none';
        }
    });
    
    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    
    if (document.querySelector('.single-post')) {
        const printBtn = document.createElement('button');
        printBtn.textContent = 'Print';
        printBtn.className = 'btn-secondary';
        printBtn.style.marginLeft = '1rem';
        
        const backLink = document.querySelector('.back-link');
        if (backLink) {
            backLink.parentNode.insertBefore(printBtn, backLink.nextSibling);
        }
        
        printBtn.addEventListener('click', function() {
            window.print();
        });
    }
});


function formatDate(dateString) {
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

function truncateText(text, maxLength) {
    return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
}


window.BlogUtils = {
    formatDate,
    truncateText
};