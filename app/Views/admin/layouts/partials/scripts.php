<!-- jQuery sudah dimuat di header -->
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- ApexCharts for beautiful charts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            boundary: document.body
        });
    });

    // Mobile sidebar toggle
    $(document).ready(function() {
        $('#sidebarToggle, #navbarToggler').on('click', function() {
            $('#sidebar').toggleClass('show');

            // Change icon based on sidebar state
            if ($('#sidebar').hasClass('show')) {
                $(this).find('i').removeClass('bi-list').addClass('bi-x');
            } else {
                $(this).find('i').removeClass('bi-x').addClass('bi-list');
            }
        });

        // Close sidebar when clicking outside on mobile
        $(document).on('click', function(e) {
            if ($(window).width() < 768) {
                if (!$(e.target).closest('#sidebar').length &&
                    !$(e.target).closest('#sidebarToggle').length &&
                    !$(e.target).closest('#navbarToggler').length &&
                    $('#sidebar').hasClass('show')) {
                    $('#sidebar').removeClass('show');
                    $('#sidebarToggle, #navbarToggler').find('i').removeClass('bi-x').addClass('bi-list');
                }
            }
        });

        // Handle window resize
        $(window).resize(function() {
            if ($(window).width() >= 768) {
                $('#sidebar').removeClass('show');
                $('#sidebarToggle, #navbarToggler').find('i').removeClass('bi-x').addClass('bi-list');
            }
        });

        // Create bubble elements for water effect
        createBubbles();
    });

    // Create bubbles for water effect
    function createBubbles() {
        const mainContent = document.querySelector('.main-content');
        const bubbleCount = 10;

        for (let i = 0; i < bubbleCount; i++) {
            const bubble = document.createElement('div');
            bubble.classList.add('bubble');

            const size = Math.random() * 100 + 50;
            const left = Math.random() * 100;
            const top = Math.random() * 100;
            const delay = Math.random() * 5;

            bubble.style.width = size + 'px';
            bubble.style.height = size + 'px';
            bubble.style.left = left + '%';
            bubble.style.top = top + '%';
            bubble.style.animationDelay = delay + 's';

            mainContent.appendChild(bubble);
        }
    }
</script>

<!-- Modal wrapper for handling modal backdrop correctly -->
<div id="modal-container"></div>
<script>
    // Move all modals to the end of body to ensure they work correctly
    $(document).ready(function() {
        // Move all modals to modal container at the end of body
        $('.modal').appendTo('#modal-container');

        // Fix modal backdrop handling
        $(document).on('show.bs.modal', '.modal', function() {
            const $modal = $(this);
            const modalZIndex = 1060;

            $modal.css('z-index', modalZIndex);

            // Make sure there's only one backdrop
            if ($('.modal-backdrop').length === 0) {
                $('<div class="modal-backdrop show"></div>')
                    .css('z-index', modalZIndex - 5)
                    .appendTo('body');
            }

            $('body').addClass('modal-open');
        });

        $(document).on('hidden.bs.modal', '.modal', function() {
            // Only remove backdrop and modal-open class if no modal is visible
            if ($('.modal:visible').length === 0) {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#btn-logout').click(function() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan keluar dari sistem!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0088cc',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Ya, Keluar!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= site_url('auth/logout') ?>',
                        type: 'GET',
                        success: function(response) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Anda telah berhasil keluar',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '<?= site_url('auth') ?>';
                            });
                        }
                    });
                }
            });
        });
    });
</script>