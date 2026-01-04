/**
 * Developer Debug Toolbar
 * Visual debugging panel for development
 */

(function() {
	'use strict';
	
	// Only run in development mode
	if (!window.location.hostname.includes('localhost') && 
	    !window.location.hostname.includes('127.0.0.1') &&
	    !window.location.hostname.includes('.local') &&
	    !window.location.hostname.includes('.test')) {
		return;
	}
	
	class DebugToolbar {
		constructor() {
			this.isVisible = localStorage.getItem('debug-toolbar-visible') === 'true';
			this.activeTab = 'console';
			this.logs = [];
			this.errors = [];
			this.warnings = [];
			this.networkRequests = [];
			this.init();
		}
		
		init() {
			this.createToolbar();
			this.interceptConsole();
			this.interceptNetwork();
			this.monitorPerformance();
			this.setupKeyboardShortcuts();
			
			if (this.isVisible) {
				this.show();
			}
		}
		
		createToolbar() {
			this.toolbar = document.createElement('div');
			this.toolbar.id = 'swg-debug-toolbar';
			this.toolbar.className = 'swg-debug-toolbar';
			this.toolbar.innerHTML = `
				<div class="debug-toolbar-header">
					<div class="debug-toolbar-logo">🛠️ SWG Debug</div>
					<div class="debug-toolbar-tabs">
						<button class="debug-tab active" data-tab="console">Console</button>
						<button class="debug-tab" data-tab="network">Network</button>
						<button class="debug-tab" data-tab="performance">Performance</button>
						<button class="debug-tab" data-tab="info">Info</button>
					</div>
					<div class="debug-toolbar-actions">
						<button class="debug-action" id="debug-clear">Clear</button>
						<button class="debug-action" id="debug-minimize">_</button>
						<button class="debug-action" id="debug-close">×</button>
					</div>
				</div>
				<div class="debug-toolbar-content">
					<div class="debug-panel active" data-panel="console">
						<div class="debug-console-output"></div>
					</div>
					<div class="debug-panel" data-panel="network">
						<div class="debug-network-output"></div>
					</div>
					<div class="debug-panel" data-panel="performance">
						<div class="debug-performance-output"></div>
					</div>
					<div class="debug-panel" data-panel="info">
						<div class="debug-info-output"></div>
					</div>
				</div>
				<div class="debug-toolbar-footer">
					<span class="debug-footer-item">Logs: <strong id="debug-log-count">0</strong></span>
					<span class="debug-footer-item">Errors: <strong id="debug-error-count">0</strong></span>
					<span class="debug-footer-item">Network: <strong id="debug-network-count">0</strong></span>
					<span class="debug-footer-item">Memory: <strong id="debug-memory">0 MB</strong></span>
				</div>
			`;
			
			document.body.appendChild(this.toolbar);
			this.setupEventListeners();
		}
		
		setupEventListeners() {
			// Tab switching
			this.toolbar.querySelectorAll('.debug-tab').forEach(tab => {
				tab.addEventListener('click', (e) => {
					const tabName = e.target.dataset.tab;
					this.switchTab(tabName);
				});
			});
			
			// Clear button
			document.getElementById('debug-clear').addEventListener('click', () => {
				this.clearLogs();
			});
			
			// Minimize button
			document.getElementById('debug-minimize').addEventListener('click', () => {
				this.toolbar.classList.toggle('minimized');
			});
			
			// Close button
			document.getElementById('debug-close').addEventListener('click', () => {
				this.hide();
			});
			
			// Draggable
			this.makeDrawerDraggable();
		}
		
		switchTab(tabName) {
			this.activeTab = tabName;
			
			this.toolbar.querySelectorAll('.debug-tab').forEach(tab => {
				tab.classList.toggle('active', tab.dataset.tab === tabName);
			});
			
			this.toolbar.querySelectorAll('.debug-panel').forEach(panel => {
				panel.classList.toggle('active', panel.dataset.panel === tabName);
			});
			
			// Update content
			if (tabName === 'console') {
				this.updateConsolePanel();
			} else if (tabName === 'network') {
				this.updateNetworkPanel();
			} else if (tabName === 'performance') {
				this.updatePerformancePanel();
			} else if (tabName === 'info') {
				this.updateInfoPanel();
			}
		}
		
		interceptConsole() {
			const originalLog = console.log;
			const originalError = console.error;
			const originalWarn = console.warn;
			
			console.log = (...args) => {
				originalLog.apply(console, args);
				this.addLog('log', args);
			};
			
			console.error = (...args) => {
				originalError.apply(console, args);
				this.addLog('error', args);
			};
			
			console.warn = (...args) => {
				originalWarn.apply(console, args);
				this.addLog('warn', args);
			};
			
			// Capture errors
			window.addEventListener('error', (e) => {
				this.addLog('error', [e.message, e.filename, e.lineno]);
			});
		}
		
		addLog(type, args) {
			const log = {
				type,
				message: args.map(arg => {
					if (typeof arg === 'object') {
						return JSON.stringify(arg, null, 2);
					}
					return String(arg);
				}).join(' '),
				timestamp: new Date().toLocaleTimeString()
			};
			
			if (type === 'error') {
				this.errors.push(log);
			} else if (type === 'warn') {
				this.warnings.push(log);
			}
			
			this.logs.push(log);
			
			// Update counts
			document.getElementById('debug-log-count').textContent = this.logs.length;
			document.getElementById('debug-error-count').textContent = this.errors.length;
			
			if (this.activeTab === 'console') {
				this.updateConsolePanel();
			}
		}
		
		updateConsolePanel() {
			const output = this.toolbar.querySelector('.debug-console-output');
			output.innerHTML = '';
			
			this.logs.slice(-100).forEach(log => {
				const logEl = document.createElement('div');
				logEl.className = `debug-log-entry debug-log-${log.type}`;
				logEl.innerHTML = `
					<span class="debug-log-time">${log.timestamp}</span>
					<span class="debug-log-type">[${log.type.toUpperCase()}]</span>
					<span class="debug-log-message">${this.escapeHtml(log.message)}</span>
				`;
				output.appendChild(logEl);
			});
			
			output.scrollTop = output.scrollHeight;
		}
		
		interceptNetwork() {
			const originalFetch = window.fetch;
			
			window.fetch = async (...args) => {
				const startTime = performance.now();
				const url = args[0];
				
				try {
					const response = await originalFetch.apply(window, args);
					const endTime = performance.now();
					
					this.networkRequests.push({
						url: url.toString(),
						method: args[1]?.method || 'GET',
						status: response.status,
						duration: Math.round(endTime - startTime),
						timestamp: new Date().toLocaleTimeString()
					});
					
					document.getElementById('debug-network-count').textContent = this.networkRequests.length;
					
					return response;
				} catch (error) {
					const endTime = performance.now();
					
					this.networkRequests.push({
						url: url.toString(),
						method: args[1]?.method || 'GET',
						status: 'Failed',
						duration: Math.round(endTime - startTime),
						timestamp: new Date().toLocaleTimeString(),
						error: error.message
					});
					
					throw error;
				}
			};
			
			// Intercept jQuery AJAX if available
			if (typeof jQuery !== 'undefined') {
				jQuery(document).ajaxComplete((event, xhr, settings) => {
					this.networkRequests.push({
						url: settings.url,
						method: settings.type,
						status: xhr.status,
						duration: 0,
						timestamp: new Date().toLocaleTimeString()
					});
					
					document.getElementById('debug-network-count').textContent = this.networkRequests.length;
				});
			}
		}
		
		updateNetworkPanel() {
			const output = this.toolbar.querySelector('.debug-network-output');
			output.innerHTML = '<table class="debug-network-table"><thead><tr><th>Time</th><th>Method</th><th>URL</th><th>Status</th><th>Duration</th></tr></thead><tbody></tbody></table>';
			
			const tbody = output.querySelector('tbody');
			
			this.networkRequests.slice(-50).forEach(req => {
				const row = document.createElement('tr');
				row.className = req.error ? 'debug-network-error' : '';
				row.innerHTML = `
					<td>${req.timestamp}</td>
					<td><span class="debug-method-badge">${req.method}</span></td>
					<td class="debug-url" title="${this.escapeHtml(req.url)}">${this.truncate(req.url, 50)}</td>
					<td><span class="debug-status-${String(req.status).startsWith('2') ? 'success' : 'error'}">${req.status}</span></td>
					<td>${req.duration}ms</td>
				`;
				tbody.appendChild(row);
			});
		}
		
		monitorPerformance() {
			setInterval(() => {
				if (performance.memory) {
					const memoryMB = (performance.memory.usedJSHeapSize / 1048576).toFixed(2);
					document.getElementById('debug-memory').textContent = memoryMB + ' MB';
				}
			}, 1000);
		}
		
		updatePerformancePanel() {
			const output = this.toolbar.querySelector('.debug-performance-output');
			
			const metrics = {
				'DOM Ready': performance.timing.domContentLoadedEventEnd - performance.timing.navigationStart,
				'Window Load': performance.timing.loadEventEnd - performance.timing.navigationStart,
				'DOM Nodes': document.querySelectorAll('*').length,
				'Scripts': document.querySelectorAll('script').length,
				'Stylesheets': document.querySelectorAll('link[rel="stylesheet"]').length,
				'Images': document.querySelectorAll('img').length
			};
			
			if (performance.memory) {
				metrics['JS Heap Size'] = this.formatBytes(performance.memory.usedJSHeapSize);
				metrics['JS Heap Limit'] = this.formatBytes(performance.memory.jsHeapSizeLimit);
			}
			
			output.innerHTML = '<div class="debug-metrics-grid"></div>';
			const grid = output.querySelector('.debug-metrics-grid');
			
			for (const [label, value] of Object.entries(metrics)) {
				const metric = document.createElement('div');
				metric.className = 'debug-metric';
				metric.innerHTML = `
					<div class="debug-metric-label">${label}</div>
					<div class="debug-metric-value">${value}</div>
				`;
				grid.appendChild(metric);
			}
		}
		
		updateInfoPanel() {
			const output = this.toolbar.querySelector('.debug-info-output');
			
			const info = {
				'User Agent': navigator.userAgent,
				'Viewport': `${window.innerWidth} × ${window.innerHeight}`,
				'Screen': `${screen.width} × ${screen.height}`,
				'Color Depth': screen.colorDepth + ' bits',
				'Pixel Ratio': window.devicePixelRatio,
				'Language': navigator.language,
				'Platform': navigator.platform,
				'Online': navigator.onLine ? 'Yes' : 'No',
				'Cookies Enabled': navigator.cookieEnabled ? 'Yes' : 'No',
				'Local Storage': typeof localStorage !== 'undefined' ? 'Available' : 'Not Available',
				'Service Worker': 'serviceWorker' in navigator ? 'Supported' : 'Not Supported'
			};
			
			output.innerHTML = '<table class="debug-info-table"></table>';
			const table = output.querySelector('table');
			
			for (const [label, value] of Object.entries(info)) {
				const row = document.createElement('tr');
				row.innerHTML = `
					<td class="debug-info-label">${label}</td>
					<td class="debug-info-value">${this.escapeHtml(String(value))}</td>
				`;
				table.appendChild(row);
			}
		}
		
		clearLogs() {
			this.logs = [];
			this.errors = [];
			this.warnings = [];
			this.networkRequests = [];
			
			document.getElementById('debug-log-count').textContent = '0';
			document.getElementById('debug-error-count').textContent = '0';
			document.getElementById('debug-network-count').textContent = '0';
			
			this.updateConsolePanel();
			this.updateNetworkPanel();
		}
		
		show() {
			this.toolbar.classList.add('visible');
			this.isVisible = true;
			localStorage.setItem('debug-toolbar-visible', 'true');
		}
		
		hide() {
			this.toolbar.classList.remove('visible');
			this.isVisible = false;
			localStorage.setItem('debug-toolbar-visible', 'false');
		}
		
		toggle() {
			if (this.isVisible) {
				this.hide();
			} else {
				this.show();
			}
		}
		
		setupKeyboardShortcuts() {
			document.addEventListener('keydown', (e) => {
				// Ctrl+Shift+D to toggle toolbar
				if (e.ctrlKey && e.shiftKey && e.key === 'D') {
					e.preventDefault();
					this.toggle();
				}
			});
		}
		
		makeDrawerDraggable() {
			const header = this.toolbar.querySelector('.debug-toolbar-header');
			let isDragging = false;
			let currentX, currentY, initialX, initialY;
			
			header.addEventListener('mousedown', (e) => {
				if (e.target.closest('.debug-toolbar-tabs') || e.target.closest('.debug-toolbar-actions')) {
					return;
				}
				
				isDragging = true;
				initialX = e.clientX - (parseInt(this.toolbar.style.left) || 0);
				initialY = e.clientY - (parseInt(this.toolbar.style.top) || 0);
			});
			
			document.addEventListener('mousemove', (e) => {
				if (!isDragging) return;
				
				e.preventDefault();
				currentX = e.clientX - initialX;
				currentY = e.clientY - initialY;
				
				this.toolbar.style.left = currentX + 'px';
				this.toolbar.style.top = currentY + 'px';
				this.toolbar.style.bottom = 'auto';
				this.toolbar.style.right = 'auto';
			});
			
			document.addEventListener('mouseup', () => {
				isDragging = false;
			});
		}
		
		// Utility functions
		escapeHtml(text) {
			const div = document.createElement('div');
			div.textContent = text;
			return div.innerHTML;
		}
		
		truncate(str, length) {
			return str.length > length ? str.substring(0, length) + '...' : str;
		}
		
		formatBytes(bytes) {
			const units = ['B', 'KB', 'MB', 'GB'];
			let i = 0;
			while (bytes >= 1024 && i < units.length - 1) {
				bytes /= 1024;
				i++;
			}
			return bytes.toFixed(2) + ' ' + units[i];
		}
	}
	
	// Initialize and expose globally
	window.debugToolbar = new DebugToolbar();
	
	// Show toolbar by default in development
	setTimeout(() => {
		if (localStorage.getItem('debug-toolbar-first-run') !== 'false') {
			window.debugToolbar.show();
			localStorage.setItem('debug-toolbar-first-run', 'false');
			console.log('%c🛠️ Debug Toolbar Loaded', 'color: #28a745; font-size: 14px; font-weight: bold;');
			console.log('%cPress Ctrl+Shift+D to toggle the debug toolbar', 'color: #6c757d; font-size: 12px;');
		}
	}, 1000);
	
})();
