import { Component } from '@angular/core'

import { NgbModal } from '@ng-bootstrap/ng-bootstrap'
import { AddModalComponent } from './add-modal/add-modal.component'

import { SOUNDS } from './temp-sounds'
import { Sound } from './sound'

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  title = 'Soundboard'

  sounds = SOUNDS

  constructor (private modalService: NgbModal) {}

  addSound () {
    const modalRef = this.modalService.open(AddModalComponent)
    modalRef.componentInstance.sound = new Sound()

    modalRef.result.then((result) => {
      console.log(result)
    }).catch((error) => {
      console.log(error)
    })
  }
}
