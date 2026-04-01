document.addEventListener('DOMContentLoaded', function() {
    var buttons = document.querySelectorAll('.insert-placeholder-btn');
    
    buttons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var placeholder = this.getAttribute('data-placeholder');
            var txtarea = document.getElementById("template_body_id");
            
            if (!txtarea) return;
            
            var scrollPos = txtarea.scrollTop;
            var caretPos = txtarea.selectionStart;
            var front = (txtarea.value).substring(0, caretPos);
            var back = (txtarea.value).substring(txtarea.selectionEnd, txtarea.value.length);
            
            txtarea.value = front + placeholder + back;
            caretPos = caretPos + placeholder.length;
            txtarea.selectionStart = caretPos;
            txtarea.selectionEnd = caretPos;
            txtarea.focus();
            txtarea.scrollTop = scrollPos;
        });
    });
});