/*
 * Toggle checkboxes of all visible taxonomies if `checkbox_select_all` is toggled
 */

let checkbox_select_all = document.getElementById('checkbox_select_all');
if (checkbox_select_all) {
    checkbox_select_all.addEventListener('change', (x) => change_all_taxonomies(x.target));
    let checkbox_taxonomies = document.getElementsByClassName('checkbox-taxonomy');
    if (checkbox_taxonomies) {
        for (let i = 0; i < checkbox_taxonomies.length; i++) {
            checkbox_taxonomies[i].addEventListener('change', (x) => change_child_metas(x.target));
        }
    }

    function change_child_metas(target) {
        let sub_checkboxes = document.getElementsByClassName('checkbox-meta-' + target.dataset.target);
        checkbox_toggle(sub_checkboxes, target.checked)
    }

    function change_all_taxonomies(target) {
        for (let item of checkbox_taxonomies) {
            item.checked = target.checked;
            change_child_metas(item);
        }
    }

    function checkbox_toggle(checkboxes, state) {
        for (let checkbox of checkboxes) {
            checkbox.checked = state
        }
    }
}
