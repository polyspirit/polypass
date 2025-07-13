class NoteEditor {
    /**
     * Universal NoteEditor for any container
     * @param {object} Quill - Quill library
     * @param {object} hljs - highlight.js library
     * @param {string} containerId - id of the container (e.g. 'note-editor')
     */
    constructor(Quill, hljs, containerId = 'note-editor') {
        this.container = document.getElementById(containerId);
        if (!this.container) {
            console.log(`NoteEditor: Container with id '${containerId}' not found`);
            return; // Exit if element doesn't exist
        }
        
        this.textarea = document.getElementById('textarea-note');
        if (!this.textarea) {
            console.log(`NoteEditor: Textarea with id 'textarea-note' not found for container '${containerId}'`);
            return; // Exit if textarea doesn't exist
        }
        
        this.readonly = parseInt(this.container.dataset.readonly);
        this.hideTextarea();

        console.log(`NoteEditor: Initializing for container '${containerId}'`);
        console.log(`NoteEditor: Initial content:`, this.container.dataset.initialContent);

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
                console.log(`NoteEditor: Parsed initial content:`, initialContent);
            } catch (e) {
                console.log(`NoteEditor: Error parsing initial content:`, e);
                // If parsing fails, treat as plain text
                const plainText = this.container.dataset.initialContent;
                initialContent = { ops: [{ insert: plainText }] };
                console.log(`NoteEditor: Converting plain text to Quill format:`, initialContent);
            }
        } else {
            try {
                initialContent = this.textarea.value.trim() ? JSON.parse(this.textarea.value) : { ops: [{ insert: '\n' }] };
            } catch (e) {
                console.log(`NoteEditor: Error parsing textarea content:`, e);
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
