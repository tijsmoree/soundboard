import { Component } from '@angular/core';

//import { SoundComponent } from './sound/sound.component'

import { SOUNDS } from './temp-sounds'

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  title = 'Soundboard';

  sounds = SOUNDS;
}
