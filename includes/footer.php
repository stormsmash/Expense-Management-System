<div id="btn-top-scroll" title="กลับขึ้นด้านบน">
    <i class="bi bi-chevron-up fs-5"></i>
</div>

<!-- Additional page scripts -->
<script src="../assets/js/jquery.datetimepicker.js"></script>

<script>
function logout() {
    Swal.fire({
        title: 'ยืนยันการออกจากระบบ?',
        text: 'คุณต้องการสิ้นสุดเซสชั่นการทำงานในระบบหรือไม่',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="bi bi-box-arrow-right me-1"></i> ออกจากระบบ',
        cancelButtonText: 'ยกเลิก',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-4'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = '../auth/logout.php';
        }
    });
}

// Scroll to top behavior
const topBtn = document.getElementById('btn-top-scroll');
if (topBtn) {
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            topBtn.style.display = 'flex';
        } else {
            topBtn.style.display = 'none';
        }
    });

    topBtn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}
</script>
</body>
</html>
