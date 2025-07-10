import './bootstrap';
import { initLegalEditor, LegalEditorConfig } from './editor/index.js';

// Disponibilizar globalmente
window.LegalEditor = {
  init: initLegalEditor,
  config: LegalEditorConfig
};
