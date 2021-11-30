// Created New Page
function newPage() {
  var name = prompt('Slug/File name');
  if (name) {
    document.location = 'editor.php?new_page=' + name;
  }
}
// SimpleMDE Config
var simplemde = new SimpleMDE({
  element: document.getElementById("MyID"),
  //autofocus: true,
  //lineWrapping: false,
  placeholder: "Type HTML here...",
  spellChecker: false,
  status: false,
  renderingConfig: {
    singleLineBreaks: false,
    codeSyntaxHighlighting: true,
  },
  toolbar: ["preview", "side-by-side", "fullscreen"],
});

// Textarea AutoSize - https://www.jacklmoore.com/autosize/
autosize(document.querySelectorAll('textarea'));