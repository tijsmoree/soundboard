import { Component, OnInit, Input } from '@angular/core'

import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap'
import { Sound } from '../sound'

@Component({
  selector: 'app-add-modal',
  templateUrl: './add-modal.component.html',
  styleUrls: ['./add-modal.component.css']
})

export class AddModalComponent implements OnInit {

  @Input() sound: Sound

  constructor (public activeModal: NgbActiveModal) { }

  ngOnInit () {
    //
  }

  close () {
    this.activeModal.close()
  }

  save () {
    this.activeModal.close(this.sound)
  }
}
