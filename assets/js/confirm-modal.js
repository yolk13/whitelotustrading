(function() {
    var modalHtml =
        '<div id="confirmModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-deep-royal/20 backdrop-blur-sm">' +
        '  <div class="bg-pure-white rounded-xl max-w-md w-full mx-4 p-6 shadow-2xl">' +
        '    <h3 class="font-headline-sm text-headline-sm text-deep-royal mb-2" id="confirmTitle">Confirm</h3>' +
        '    <p class="text-on-surface-variant text-sm mb-6" id="confirmMessage">Are you sure?</p>' +
        '    <p class="text-xs text-on-surface-variant/60 mb-6" id="confirmNote"></p>' +
        '    <div class="flex justify-end gap-3">' +
        '      <button id="confirmCancel" class="px-5 py-2.5 rounded-lg font-label-caps border border-divider-gray hover:bg-surface-container transition-all">Cancel</button>' +
        '      <button id="confirmOk" class="px-5 py-2.5 rounded-lg font-label-caps text-pure-white bg-error hover:brightness-110 transition-all shadow-sm">Delete</button>' +
        '    </div>' +
        '  </div>' +
        '</div>';

    document.addEventListener('DOMContentLoaded', function() {
        var existing = document.getElementById('confirmModal');
        if (!existing) {
            var div = document.createElement('div');
            div.innerHTML = modalHtml;
            document.body.appendChild(div.firstElementChild);
        }

        document.querySelectorAll('[data-confirm]').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                var msg = el.getAttribute('data-confirm') || 'Are you sure?';
                var note = el.getAttribute('data-confirm-note') || '';
                var title = el.getAttribute('data-confirm-title') || 'Confirm';
                var okText = el.getAttribute('data-confirm-ok') || 'Delete';
                var form = el.closest('form');
                var href = el.getAttribute('href');

                showConfirmModal(title, msg, note, okText, function() {
                    if (form) form.submit();
                    else if (href) window.location.href = href;
                });
            });
        });

        var forms = document.querySelectorAll('form[onsubmit]');
        forms.forEach(function(form) {
            var orig = form.getAttribute('onsubmit');
            if (orig && orig.includes('confirm(')) {
                form.removeAttribute('onsubmit');
                var submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.setAttribute('data-confirm', orig.match(/confirm\('([^']+)'\)/)?.[1] || 'Are you sure?');
                    submitBtn.setAttribute('data-confirm-ok', 'Delete');
                    submitBtn.setAttribute('data-confirm-title', 'Confirm Deletion');
                    submitBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        var msg = submitBtn.getAttribute('data-confirm');
                        showConfirmModal('Confirm Deletion', msg, 'This action can be undone in Trash within 30 days.', 'Move to Trash', function() {
                            form.submit();
                        });
                    });
                }
            }
        });
    });

    function showConfirmModal(title, message, note, okText, callback) {
        var modal = document.getElementById('confirmModal');
        if (!modal) return;
        document.getElementById('confirmTitle').textContent = title;
        document.getElementById('confirmMessage').textContent = message;
        var noteEl = document.getElementById('confirmNote');
        if (note) {
            noteEl.textContent = note;
            noteEl.classList.remove('hidden');
        } else {
            noteEl.classList.add('hidden');
        }
        document.getElementById('confirmOk').textContent = okText || 'Delete';

        document.getElementById('confirmOk').onclick = function() {
            modal.classList.add('hidden');
            if (callback) callback();
        };
        document.getElementById('confirmCancel').onclick = function() {
            modal.classList.add('hidden');
        };

        modal.classList.remove('hidden');
    }

    window.showConfirmModal = showConfirmModal;
})();
