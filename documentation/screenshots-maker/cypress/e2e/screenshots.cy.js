Cypress.Screenshot.defaults({
    capture: 'viewport',
})

Cypress.Commands.add('loginAsAdmin', () => {
    cy.env(['adminEmail', 'adminPassword']).then(env => {
        cy.visit('/login')
        cy.get('input[name="email"]').type(env.adminEmail);
        cy.get('input[name="password"]').type(env.adminPassword);
        cy.get('#loginform input[type="submit"]').click();
    });
});

describe('admin', { testIsolation: false }, () => {
    before(function() {
        cy.loginAsAdmin();
        const omekaLang = Cypress.expose('omekaLang');
        if (omekaLang) {
            cy.visit('/admin/setting');
            cy.get('#locale').select(omekaLang, { force: true });
            cy.get('#page-actions button').click();
        }
    })
    it('takes screenshots for documentation', () => {
        // Go to Formularium admin
        cy.visit('/admin/formularium');

        // Screenshot
        cy.screenshot('images/form-browse-empty');
    })

    it('takes screenshots for documentation again', () => {
        // Go to Formularium admin
        cy.visit('/admin/formularium');

        // Screenshot
        cy.screenshot('images/form-browse-empty-2');
    })
})
