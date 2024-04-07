import './bootstrap';

import './form';

import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import hljs from 'highlight.js';
import 'highlight.js/styles/atom-one-dark.min.css';

import Copier from './classes/Copier';
import PasswordGeneratorForm from './classes/PasswordGeneratorForm';
import CredentialRemote from './classes/CredentialRemote';
import Button from './classes/Button';
import NoteEditor from './classes/NoteEditor';

new Copier();
new PasswordGeneratorForm();
new CredentialRemote();
new Button();
new NoteEditor(Quill, hljs);
