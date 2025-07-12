class NoteEditor {

    constructor(Quill, hljs) {
        this.container = document.getElementById('note-editor');
        this.textarea = document.getElementById('textarea-note');
        this.readonly = parseInt(this.container.dataset.readonly);
        this.hideTextarea();

        const quill = new Quill('#note-editor', {
            readOnly: this.readonly,
            modules: {
                toolbar: this.readonly ? false : this.getToolbarOptions(),
                syntax: { hljs },
            },
            theme: 'snow'
        });

        // Get initial content from data attribute or textarea
        let initialContent;
        if (this.container.dataset.initialContent) {
            try {
                initialContent = JSON.parse(this.container.dataset.initialContent);
            } catch (e) {
                initialContent = { ops: [{ insert: '\n' }] };
            }
        } else {
            initialContent = this.textarea.value.trim() ? JSON.parse(this.textarea.value) : { ops: [{ insert: '\n' }] };
        }
        quill.setContents(initialContent);

        quill.on('text-change', (delta, oldDelta, source) => {
            if (source === 'user') {
                const contents = quill.getContents();
                this.textarea.value = JSON.stringify(contents);
            }
        });

        // Ensure form data is updated before submission
        const form = this.container.closest('form');
        if (form) {
            form.addEventListener('submit', () => {
                const contents = quill.getContents();
                const jsonString = JSON.stringify(contents);
                this.textarea.value = jsonString;
            });
        }
    }

    hideTextarea() {
        this.textarea.style.display = 'none';
    }

    getToolbarOptions() {
        return [
            ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
            ['blockquote', 'code-block'],
            ['link', 'image', 'video', 'formula'],

            [{ 'header': 1 }, { 'header': 2 }],               // custom button values
            [{ 'list': 'ordered' }, { 'list': 'bullet' }, { 'list': 'check' }],
            [{ 'script': 'sub' }, { 'script': 'super' }],     // superscript/subscript
            [{ 'indent': '-1' }, { 'indent': '+1' }],         // outdent/indent
            [{ 'direction': 'rtl' }],                         // text direction

            [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdow
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

            [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
            [{ 'font': [] }],
            [{ 'align': [] }],

            ['clean']                                         // remove formatting button
        ];
    }
}

module.exports = NoteEditor;
