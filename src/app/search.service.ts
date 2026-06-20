import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class SearchService {
  searchTerm$ = new Subject<string>();

  updateSearch(value: string) {
    this.searchTerm$.next(value);
  }
}