import './bootstrap';

import './form';

// Quill and highlight.js are loaded via CDN
// They are available as global objects: window.Quill and window.hljs

import Copier from './classes/Copier';
import PasswordGeneratorForm from './classes/PasswordGeneratorForm';
import CredentialRemote from './classes/CredentialRemote';
import Button from './classes/Button';

// Initialize other components
new Copier();
new PasswordGeneratorForm();
new CredentialRemote();
new Button();

// Initialize NoteEditor with proper library loading checks
import './note-editor-init';
