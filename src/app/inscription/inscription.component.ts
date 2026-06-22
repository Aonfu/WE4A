import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';

@Component({
  standalone: true,
  imports: [CommonModule, RouterModule],
  selector: 'app-inscription',
  templateUrl: './inscription.component.html'
})
export class InscriptionComponent {
  erreur = '';

  constructor(private http: HttpClient, private router: Router) {}

  inscrire(nom: string, prenom: string, email: string, mdp: string, confirmMdp: string) {
    if (mdp !== confirmMdp) {
      this.erreur = 'Les mots de passe ne sont pas identiques !';
      return;
    }
    this.erreur = '';
    this.http.post<any>('/api/inscription.php', { nom, prenom, email, mdp }).subscribe(data => {
      if (data.success) {
        // Log d'inscription
        this.http
          .post('http://localhost:3000/api/logs/create', {
            userId: email,
            action: 'register',
          })
          .subscribe();

        this.router.navigate(['/catalogue']);
      } else {
        this.erreur = data.error;
      }
    });
  }
}
