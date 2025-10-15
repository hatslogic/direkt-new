import FileUploadPlugin from './file-upload/file-upload.plugin';
const PluginManager = window.PluginManager;

PluginManager.register('FileUploadPlugin', FileUploadPlugin, '[data-file-upload]');

// Important for the webpack hot module reloading server.
if (module.hot) {
    module.hot.accept();
}