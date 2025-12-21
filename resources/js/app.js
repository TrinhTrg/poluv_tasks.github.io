import './bootstrap';
import './theme';
import Alpine from 'alpinejs';

// Make Alpine available globally BEFORE Livewire loads
window.Alpine = Alpine;

// IMPORTANT: Don't call Alpine.start() here
// Let Livewire handle starting Alpine to ensure proper integration
