(function() {
    var AUTOSAVE_KEY = 'wl_autosave';
    var SAVE_DELAY = 2000;
    var saveTimer = null;

    function getFormId(form) {
        return form.getAttribute('name') || form.getAttribute('id') || window.location.pathname;
    }

    function collectFormData(form) {
        var data = {};
        var fields = form.querySelectorAll('[name]:not([type="hidden"]):not([type="password"]):not([name="csrf_token"])');
        fields.forEach(function(field) {
            if (field.type === 'file') return;
            if (field.type === 'checkbox' || field.type === 'radio') {
                if (field.checked) data[field.name] = field.value;
                return;
            }
            data[field.name] = field.value;
        });
        if (typeof tinymce !== 'undefined') {
            tinymce.editors.forEach(function(editor) {
                var textarea = editor.getElement();
                if (textarea && textarea.name) {
                    data[textarea.name] = editor.getContent();
                }
            });
        }
        return data;
    }

    function restoreFormData(form) {
        try {
            var store = JSON.parse(localStorage.getItem(AUTOSAVE_KEY) || '{}');
            var saved = store[getFormId(form)];
            if (!saved) return false;

            var contentChanged = false;
            Object.keys(saved).forEach(function(name) {
                var field = form.querySelector('[name="' + CSS.escape(name) + '"]');
                if (!field) return;
                if (field.type === 'file' || field.type === 'password') return;
                if (typeof tinymce !== 'undefined' && tinymce.get(name)) {
                    if (tinymce.get(name).getContent() !== saved[name]) {
                        tinymce.get(name).setContent(saved[name]);
                        contentChanged = true;
                    }
                    return;
                }
                if (field.value !== saved[name] && !field.value) {
                    field.value = saved[name];
                    contentChanged = true;
                }
            });
            return contentChanged;
        } catch(e) {
            return false;
        }
    }

    function saveToStorage(form) {
        try {
            var store = JSON.parse(localStorage.getItem(AUTOSAVE_KEY) || '{}');
            store[getFormId(form)] = collectFormData(form);
            localStorage.setItem(AUTOSAVE_KEY, JSON.stringify(store));
        } catch(e) {}
    }

    function clearSaved(form) {
        try {
            var store = JSON.parse(localStorage.getItem(AUTOSAVE_KEY) || '{}');
            delete store[getFormId(form)];
            localStorage.setItem(AUTOSAVE_KEY, JSON.stringify(store));
        } catch(e) {}
    }

    document.addEventListener('DOMContentLoaded', function() {
        var forms = document.querySelectorAll('form');
        forms.forEach(function(form) {
            var restored = restoreFormData(form);
            if (restored) {
                var banner = document.createElement('div');
                banner.className = 'bg-amber-100 text-amber-900 px-4 py-2 rounded-lg text-sm mb-4 flex items-center justify-between';
                banner.innerHTML = '<span>Draft restored from autosave.</span><button onclick="this.parentElement.remove()" class="text-xs font-bold underline">Dismiss</button>';
                form.parentElement.insertBefore(banner, form);
            }

            form.addEventListener('submit', function() {
                clearSaved(form);
            });

            var inputs = form.querySelectorAll('input:not([type="hidden"]):not([type="file"]):not([type="password"]), textarea, select');
            inputs.forEach(function(input) {
                input.addEventListener('input', function() {
                    clearTimeout(saveTimer);
                    saveTimer = setTimeout(function() { saveToStorage(form); }, SAVE_DELAY);
                });
                input.addEventListener('change', function() {
                    clearTimeout(saveTimer);
                    saveTimer = setTimeout(function() { saveToStorage(form); }, SAVE_DELAY);
                });
            });
        });
    });
})();
