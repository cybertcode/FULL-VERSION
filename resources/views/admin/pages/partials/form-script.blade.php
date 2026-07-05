<script>
'use strict';

window.addEventListener('load', function () {
  $('.select2').select2({ width: '100%' });

  // Inicializa un editor Quill por cada campo richtext (uno por plantilla,
  // solo el de la plantilla activa es visible, pero todos existen en el DOM).
  document.querySelectorAll('.quill-hidden-input').forEach(function (hiddenInput) {
    const target = document.querySelector('.' + hiddenInput.dataset.editorTarget);
    if (! target) { return; }

    const editor = new Quill(target, { theme: 'snow' });
    editor.root.innerHTML = hiddenInput.value || '';

    editor.on('text-change', function () {
      hiddenInput.value = editor.root.innerHTML;
    });
  });

  // Mostrar solo el bloque de campos de la plantilla seleccionada.
  const templateSelect = document.getElementById('template');
  function toggleTemplateFields() {
    document.querySelectorAll('.template-fields').forEach(function (block) {
      block.style.display = block.dataset.template === templateSelect.value ? '' : 'none';
    });
  }
  templateSelect.addEventListener('change', toggleTemplateFields);
  toggleTemplateFields();
});
</script>
