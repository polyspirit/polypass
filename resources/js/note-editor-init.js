import NoteEditor from './classes/NoteEditor';

// Global flag to prevent multiple initializations
let noteEditorInitialized = false;

// Function to wait for libraries to be available
function waitForLibraries() {
    return new Promise((resolve, reject) => {
        let attempts = 0;
        const maxAttempts = 150; // 15 seconds max (150 * 100ms)
        
        const checkLibraries = () => {
            attempts++;
            
            // Check if both libraries are loaded and highlight.js is properly initialized
            if (window.Quill && window.hljs && typeof window.hljs.highlightAll === 'function') {
                resolve();
            } else if (attempts >= maxAttempts) {
                console.error('NoteEditor: Libraries not available after maximum attempts');
                reject(new Error('Libraries not available'));
            } else {
                setTimeout(checkLibraries, 100);
            }
        };
        
        checkLibraries();
    });
}

// Function to initialize NoteEditor
function initializeNoteEditor() {
    // Check if already initialized
    if (noteEditorInitialized) {
        return;
    }
    
    waitForLibraries()
        .then(() => {
            try {
                new NoteEditor(window.Quill, window.hljs, 'note-editor');
                noteEditorInitialized = true;
            } catch (error) {
                console.error('NoteEditor: Error during initialization:', error);
            }
        })
        .catch((error) => {
            console.error('NoteEditor: Failed to initialize:', error);
        });
}

// Start initialization when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeNoteEditor);
} else {
    // DOM is already ready
    initializeNoteEditor();
}

// Fallback initialization after 5 seconds
setTimeout(() => {
    if (noteEditorInitialized) {
        return;
    }
    
    if (window.Quill && window.hljs) {
        try {
            new NoteEditor(window.Quill, window.hljs, 'note-editor');
            noteEditorInitialized = true;
        } catch (error) {
            console.error('NoteEditor: Fallback initialization failed:', error);
        }
    } else {
        console.error('NoteEditor: Fallback failed - libraries still not available');
    }
}, 5000); 