import { Component, OnInit, Input } from '@angular/core'

import { Sound } from '../sound'

@Component({
  selector: 'app-sound',
  templateUrl: './sound.component.html',
  styleUrls: ['./sound.component.css']
})
export class SoundComponent implements OnInit {
  @Input() sound: Sound

  constructor () {
    //
  }

  ngOnInit () {
    //
  }

  play (id: number): void {
    console.log(id)
  }
}
