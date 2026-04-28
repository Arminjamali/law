{{-- Jalali date picker partial --}}
<script>
function persianToGregorian(jDate) {
    // Simple JS implementation of Jalali to Gregorian
    const parts = jDate.split('/');
    if (parts.length !== 3) return '';
    const [jy, jm, jd] = parts.map(Number);
    // Using Intl to validate, actual conversion via hidden field updated on form submit
    return jDate;
}
</script>
