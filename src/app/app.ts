import { Component, signal, OnInit } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { HeaderComponent } from './header/header.component';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, HeaderComponent],
  templateUrl: './app.html',
  styleUrl: './app.css',
})
export class App implements OnInit {
  protected readonly title = signal('WE4B');

  constructor(private http: HttpClient) {}

  ngOnInit() {
    // pour compter les visites sur la page d'acceuil
    this.http.get('http://localhost:3000/api/stats/visit').subscribe();
  }
}
