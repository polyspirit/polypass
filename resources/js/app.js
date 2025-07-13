import './bootstrap';

import './form';

// Quill and highlight.js are loaded via CDN
// They are available as global objects: window.Quill and window.hljs

import Copier from './classes/Copier';
import PasswordGeneratorForm from './classes/PasswordGeneratorForm';
import CredentialRemote from './classes/CredentialRemote';
import Button from './classes/Button';
import NoteEditor from './classes/NoteEditor';

new Copier();
new PasswordGeneratorForm();
new CredentialRemote();
new Button();
new NoteEditor(window.Quill, window.hljs, 'note-editor');
