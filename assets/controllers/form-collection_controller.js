import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static values = {
        addLabel: String,
        deleteLabel: String
    }

    /**
    * Dynamically inject "Add" button and "Remove" buttons
    */
    connect() {
        this.index = this.element.childElementCount
        const btn = document.createElement('button')
        btn.setAttribute('class', 'btn btn-secondary')
        btn.innerText = this.addLabelValue || 'Add an element'
        btn.setAttribute('type', 'button')
        btn.addEventListener('click', this.addElement)
        this.element.childNodes.forEach(this.addDeleteButton)
        this.element.append(btn)
    }

    /**
     * Adds a new entry in the HTML structure
     * 
     * @param {MouseEvent} e
     */
    addElement = (e) => {
        e.preventDefault()
        const element = document.createRange().createContextualFragment(
            this.element.dataset['prototype'].replaceAll('__name__', this.index)
        ).firstElementChild
        this.addDeleteButton(element)
        this.index++
        e.currentTarget.insertAdjacentElement('beforebegin', element)
    }

    /**
     * Add button to delete a row
     * 
     * @param {HTMLElement} item
     */
    addDeleteButton = (item) => {
        const btn = document.createElement('button')
        btn.setAttribute('class', 'btn btn-secondary')
        btn.innerText = this.deleteLabelValue || 'Delete'
        btn.setAttribute('type', 'button')
        item.append(btn)
        btn.addEventListener('click', e => {
            e.preventDefault()
            item.remove()
        })
    }
}
