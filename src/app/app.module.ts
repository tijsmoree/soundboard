import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FormsModule, ReactiveFormsModule }   from '@angular/forms';

import { NgbModule } from '@ng-bootstrap/ng-bootstrap';

import { AppComponent } from './app.component';
import { SoundComponent } from './sound/sound.component';
import { FilterPipe } from './filter.pipe';
import { AddModalComponent } from './add-modal/add-modal.component';

@NgModule({
  declarations: [
    AppComponent,
    SoundComponent,
    FilterPipe,
    AddModalComponent
  ],
  imports: [
    BrowserModule,
    NgbModule.forRoot(),
    FormsModule,
    ReactiveFormsModule
  ],
  providers: [],
  bootstrap: [AppComponent],
  entryComponents: [
    AddModalComponent
  ]
})

export class AppModule { }
