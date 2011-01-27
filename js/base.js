function $(element) {
    if (typeof(element) !== 'string') {
        return false;
    }

    return document.getElementById(element);
}
