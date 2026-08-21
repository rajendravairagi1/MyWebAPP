import Alpine from 'alpinejs';
import { Chart, BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend } from 'chart.js';

Chart.register(BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend);
window.Chart = Chart;

window.Alpine = Alpine;

Alpine.start();
