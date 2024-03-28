import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static classes = ['toggle'];
    static outlets = ['container'];

    connect() {
        const toggleClass = this.toggleClass;
        this.element.onclick = () => {
            this.containerOutletElement.classList.toggle(toggleClass);
        }
    }
}
