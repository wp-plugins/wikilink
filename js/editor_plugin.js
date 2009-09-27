(function() {

	tinymce.create('tinymce.plugins.WIKI_customMCEPluginName', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			ed.addButton('WIKI_MCECustomButton', {
				title : 'Insert link to wikipedia',
				image : url + '/../images/wikipedia_1.jpg',
				onclick : function() {
                    tinyMCE.execCommand('mceReplaceContent', false ,'[wiki]{$selection}[/wiki]');
				}
			});
		},


	});

	// Register plugin
	tinymce.PluginManager.add('WIKI_customMCEPlugin', tinymce.plugins.WIKI_customMCEPluginName);
})();