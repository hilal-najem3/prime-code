function imageUploader(fieldName, multiple, deletedMedia) {
    return {
        files: [],
        deletedIds: [],
        deletedMedia: "deleted_media",
        previewSelectedFiles(event) {
            const selectedFiles = Array.from(event.target.files);
            const dt = new DataTransfer();

            if (!multiple) {
                // Remove previously added files
                this.files = [];

                // Remove existing images only inside this component
                this.$el.querySelectorAll(".existing-media").forEach((el) => {
                    const id = el.getAttribute("data-id");
                    if (id) this.deletedIds.push(id);
                    el.remove();
                });

                // Handle single file
                const file = selectedFiles[0];
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.files = [
                        {
                            preview: e.target.result,
                            file: file,
                        },
                    ];
                    dt.items.add(file);
                    this.$refs.fileInput.files = dt.files;
                };
                reader.readAsDataURL(file);
            } else {
                // Handle multiple files
                this.files = [];

                selectedFiles.forEach((file) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.files.push({
                            preview: e.target.result,
                            file: file,
                        });
                        dt.items.add(file);
                        this.$refs.fileInput.files = dt.files;
                    };
                    reader.readAsDataURL(file);
                });
            }
        },
        removeFile(index) {
            this.files.splice(index, 1);

            const dt = new DataTransfer();
            this.files.forEach((f) => dt.items.add(f.file));
            this.$refs.fileInput.files = dt.files;
        },
        deleteFile(file) {
            const id = file?.id;
            if (!id) return;
            this.deletedIds.push(id);
            let input = document.querySelector(
                `input[name=" ` + deletedMedia + `"]`
            );
            if (!input) {
                input = document.createElement("input");
                input.type = "hidden";
                input.name = deletedMedia;
                document.querySelector("form").appendChild(input);
            }
            input.value = this.deletedIds.join(",");
        },
        handleDrop(event) {
            event.preventDefault();
            const droppedFiles = event.dataTransfer.files;
            this.$refs.fileInput.files = droppedFiles;
            this.previewSelectedFiles({ target: { files: droppedFiles } });
        },
    };
}

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".remove-existing").forEach((btn) => {
        btn.addEventListener("click", function () {
            const container = this.closest("div");

            const hiddenInput = container.querySelector('input[type="hidden"]');
            if (hiddenInput) hiddenInput.remove();

            container.remove();
        });
    });
});
