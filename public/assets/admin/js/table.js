$(document).ready(function () {
    console.log("Table script loaded");
    const rowsPerPage = 5;
    let currentPage = 1;

    const $tableBody = $("#tableBody");
    const $searchInput = $("#searchInput");
    const $paginationControls = $("#paginationControls");

    // Clone original rows to keep a stable list
    const originalRows = $tableBody.find("tr").clone();

    function filterRows() {
        const term = $searchInput.val().toLowerCase();
        return originalRows.filter(function () {
            return $(this).text().toLowerCase().includes(term);
        });
    }

    function displayRows(filteredRows) {
        $tableBody.empty();
        const start = (currentPage - 1) * rowsPerPage;
        const paginated = filteredRows.slice(start, start + rowsPerPage);
        paginated.each(function () {
            $tableBody.append($(this));
        });
    }

    function updatePagination(filteredRows) {
        $paginationControls.empty();
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        for (let i = 1; i <= totalPages; i++) {
            const $btn = $("<button>")
                .text(i)
                .addClass("px-3 py-1 rounded border")
                .toggleClass("bg-blue-500 text-white", i === currentPage)
                .toggleClass("bg-white hover:bg-blue-100", i !== currentPage)
                .on("click", function () {
                    currentPage = i;
                    render();
                });
            $paginationControls.append($btn);
        }
    }

    function render() {
        const filtered = filterRows();
        updatePagination(filtered);
        displayRows(filtered);
    }

    $searchInput.on("input", function () {
        currentPage = 1;
        render();
    });

    render();
});
