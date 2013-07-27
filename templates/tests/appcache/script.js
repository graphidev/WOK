window.onload = function() {

	console.log('--- [debug] --- Document loaded');

				appcache = window.applicationCache;
				if(appcache) { console.log('--- [debug] --- AppCache available'); }

				

				function switchedOffline() {
					console.log('--- [debug] --- browser switched to offline');
				}
				function switchedOnline() {
					console.log('--- [debug] --- browser switched to online');				
				}

				document.addEventListener('online',  switchedOnline);
	  			document.addEventListener('offline', switchedOffline);

	  			if (window.applicationCache) {
					applicationCache.addEventListener('updateready', function() {
					    if (confirm('An update is available. Reload now?')) {
					        window.location.reload();
					    }
					});
	  			} 

}