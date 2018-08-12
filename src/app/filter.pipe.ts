import { Pipe, PipeTransform } from '@angular/core'

@Pipe({
  name: 'filter'
})
export class FilterPipe implements PipeTransform {

  transform (items: any[], field: string, filter: string): any {
    if (!items || !filter) {
      return items
    }

    return items.filter(item => item[field].toLowerCase().includes(filter.toLowerCase()))
  }
}
