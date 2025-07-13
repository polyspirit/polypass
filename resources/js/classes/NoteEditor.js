class NoteEditor {
    /**
     * Universal NoteEditor for any container
     * @param {object} Quill - Quill library
     * @param {object} hljs - highlight.js library
     * @param {string} containerId - id of the container (e.g. 'note-editor')
     */
    constructor(Quill, hljs, containerId = 'note-editor') {
        // Check if Quill is available
        if (!Quill || typeof Quill !== 'function') {
            console.error('NoteEditor: Quill is not available or not a constructor');
            return;
        }

        // Check if highlight.js is available
        if (!hljs) {
            console.error('NoteEditor: highlight.js is not available');
            return;
        }

        this.container = document.getElementById(containerId);
        if (!this.container) {
            return; // Exit if element doesn't exist
        }
        
        // Check if Quill is already initialized in this container
        if (this.container.querySelector('.ql-editor')) {
            return;
        }
        
        this.textarea = document.getElementById('textarea-note');
        if (!this.textarea) {
            return; // Exit if textarea doesn't exist
        }
        
        this.readonly = parseInt(this.container.dataset.readonly);
        this.hideTextarea();

        const quill = new Quill(`#${containerId}`, {
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
                // If parsing fails, treat as plain text
                const plainText = this.container.dataset.initialContent;
                initialContent = { ops: [{ insert: plainText }] };
            }
        } else {
            try {
                initialContent = this.textarea.value.trim() ? JSON.parse(this.textarea.value) : { ops: [{ insert: '\n' }] };
            } catch (e) {
                initialContent = { ops: [{ insert: this.textarea.value || '\n' }] };
            }
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
        if (this.textarea) {
            this.textarea.style.display = 'none';
        }
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

            [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

            [{ 'color': null }, { 'background': null }],      // dropdown with defaults from theme
            [{ 'font': null }],
            [{ 'align': null }],

            ['clean']                                         // remove formatting button
        ];
    }
}

export default NoteEditor;
