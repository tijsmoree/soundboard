import { Pipe, PipeTransform } from '@angular/core'

@Pipe({
  name: 'filter'
})
export class FilterPipe implements PipeTransform {

  transform (items: any[], filter: Object): any {
    if (!items || !filter) {
      return items
    }

    for (const field of Object.keys(filter)) {
      if (!filter[field]) {
        continue
      }
      items = items.filter(item => item[field].toLowerCase().includes(filter[field].toLowerCase()))
    }

    return items
  }
}
