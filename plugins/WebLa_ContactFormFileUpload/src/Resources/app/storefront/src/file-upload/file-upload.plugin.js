import Plugin from 'src/plugin-system/plugin.class';

export default class FileUploadPlugin extends Plugin {
	init() {
		this.allFiles = new DataTransfer();
		this.addEventListeners();
	}

	addEventListeners() {
		const inputs = this.el.querySelectorAll('.drop-zone__input');
		const dropZoneElement = this.el.querySelector('.drop-zone');
		const validExtensions = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];

		inputs.forEach((inputElement) => {
			const previewZone = dropZoneElement?.querySelector('.upload-preview');
			if (!dropZoneElement || !previewZone) {
				return;
			}

			inputElement.addEventListener('change', (e) => {
				const newFiles = inputElement.files;
				if (!newFiles) {
					return;
				}

				// Add new files to DataTransfer object
				for (let i = 0; i < newFiles.length; i++) {
					const file = newFiles.item(i);
					this.allFiles.items.add(file);
				}

				inputElement.files = this.allFiles.files;
				this.updateThumbnail(previewZone, inputElement.files);
			});

			dropZoneElement.addEventListener('dragover', (e) => {
				e.preventDefault();
				dropZoneElement.classList.add('drop-zone--over');
			});

			['dragleave', 'dragend'].forEach((type) => {
				dropZoneElement.addEventListener(type, (e) => {
					dropZoneElement.classList.remove('drop-zone--over');
				});
			});

			dropZoneElement.addEventListener('drop', (event) => {
				event.preventDefault();
				if (event.dataTransfer) {
					if (event.dataTransfer.files.length) {
						// Add new files to DataTransfer object
						for (const item of event.dataTransfer.files) {
							this.allFiles.items.add(item);
						}
						inputElement.files = this.allFiles.files;
						this.updateThumbnail(previewZone, inputElement.files);
					}
				}
			});
		});
	}

	updateRemoveButtons(dropZoneElement) {
		const buttons = document.querySelectorAll('.remove-button');
		if (buttons) {
			buttons.forEach((button) => {
				button.addEventListener('click', (e) => {
					e.preventDefault();
					const target = e.currentTarget;
					const hash = target.dataset.removeHash;
					const inputElement = dropZoneElement.parentElement?.querySelector('.drop-zone__input');
					const files = inputElement.files;
					const dt = new DataTransfer();

					if (files) {
						for (let index = 0; index < files.length; index++) {
							const file = files.item(index);
							if (file) {
								const name = file.name;
								if (this.hashCode(name).toString() !== hash) {
									dt.items.add(file);
								}
							}
						}
					}

					this.allFiles = dt; // Update the allFiles DataTransfer object
					inputElement.files = this.allFiles.files;
					this.updateThumbnail(dropZoneElement, inputElement.files);
				});
			});
		}
	}

	updateThumbnail(dropZoneElement, files) {
		dropZoneElement.replaceChildren();
		for (let index = 0; index < files.length; index++) {
			const file = files.item(index);
			let thumbnailElement = dropZoneElement.querySelector('.drop-zone__thumb' + this.hashCode(file.name));

			if (!thumbnailElement) {
				thumbnailElement = document.createElement('div');
				thumbnailElement.classList.add('drop-zone__thumb');
				thumbnailElement.classList.add('drop-zone__thumb' + this.hashCode(file.name));

				const removeButtom = document.createElement('button');
				removeButtom.type = 'button';
				removeButtom.classList.add('btn', 'remove-button', 'btn-secondary');
				removeButtom.innerHTML = '&times;';
				removeButtom.dataset.removeHash = this.hashCode(file.name).toString();
				thumbnailElement.appendChild(removeButtom);
				dropZoneElement.appendChild(thumbnailElement);
			}
			thumbnailElement.dataset.label = file.name;
			thumbnailElement.dataset.fileHash = this.hashCode(file.name).toString();

			if (file.type.startsWith('image/')) {
				const reader = new FileReader();
				reader.readAsDataURL(file);
				reader.onload = () => {
					thumbnailElement.style.backgroundImage = `url('${reader.result}')`;
				};
			} else {
				let thumbnailLabelElement = document.createElement('p');
				thumbnailLabelElement.textContent = file.name;
				thumbnailElement.appendChild(thumbnailLabelElement);
				thumbnailElement.style.backgroundImage = '';
			}
		}
		this.updateRemoveButtons(dropZoneElement);
	}

	hashCode(str) {
		return Array.from(str).reduce((s, c) => (Math.imul(31, s) + c.charCodeAt(0)) | 0, 0);
	}
}
