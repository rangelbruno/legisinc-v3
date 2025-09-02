import './bootstrap';
import { createApp } from 'vue';
import OnlyOfficeMonitor from './components/OnlyOfficeMonitor.vue';

// Registrar componente Vue globalmente
window.Vue = { createApp };
window.OnlyOfficeMonitorComponent = OnlyOfficeMonitor;

console.log('✅ Vue.js e OnlyOfficeMonitor carregados');
