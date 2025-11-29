export const formatNumber = (value: unknown) => {
    const numeric = Number(value || 0);

    try {
        const hasFraction = Math.abs(numeric % 1) > 0;

        return new Intl.NumberFormat('en-SD', {
            minimumFractionDigits: hasFraction ? 2 : 0,
            maximumFractionDigits: hasFraction ? 2 : 0,
        }).format(numeric);
    } catch (error) {
        return numeric.toLocaleString('en-SD', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2,
        });
    }
};
