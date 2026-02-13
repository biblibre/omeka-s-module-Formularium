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
Cypress.Commands.add('logout', () => {
    cy.visit('/logout');
});

describe('screenshots', () => {
    var strings;
    before(function() {
        cy.loginAsAdmin();
        const omekaLang = Cypress.expose('omekaLang');
        if (omekaLang) {
            cy.visit('/admin/setting');
            cy.get('#locale').select(omekaLang, { force: true });
            cy.get('#page-actions button').click();
        }
        cy.fixture('strings').then(_strings => { strings = _strings[omekaLang] ?? _strings[''] });
        cy.logout();
    });

    it('create form', () => {
        cy.loginAsAdmin();
        cy.visit('/admin/formularium');
        cy.screenshot('images/form-browse-empty');

        cy.get('[data-cy="add-new-form"]').click();
        cy.screenshot('images/form-add-empty');

        cy.get('[name="o:name"]').type('Contact');
        cy.get('[data-cy="add-component"]').click();
        cy.screenshot('images/form-add-component-sidebar');

        cy.get('#component-selector .option[data-type="user_email"]').click();
        cy.focused().type(strings.emailLabel)
        cy.focused().closest('.components-component').as('emailComponent');
        cy.get('@emailComponent').find('[name="settings[name]"]').type('email');
        cy.get('@emailComponent').find('[name="settings[label]"]').type(strings.emailLabel);
        cy.get('@emailComponent').find('[type="checkbox"][name="settings[required]"]').check();
        cy.screenshot('images/form-add-first-component');
        cy.get('@emailComponent').find('.components-component-edit-button').click();

        cy.get('[data-cy="add-component"]').click();
        cy.get('#component-selector .option[data-type="textarea"]').click();
        cy.focused().type(strings.messageLabel)
        cy.focused().closest('.components-component').as('messageComponent');
        cy.get('@messageComponent').find('[name="settings[name]"]').type('message');
        cy.get('@messageComponent').find('[name="settings[label]"]').type(strings.messageLabel);
        cy.get('@messageComponent').find('[type="checkbox"][name="settings[required]"]').check();
        cy.get('@messageComponent').find('.components-component-edit-button').click();

        cy.get('[data-cy="add-action"]').click();
        cy.get('#action-selector .option[data-type="email"]').click();
        cy.focused().type(strings.sendEmailLabel)
        cy.focused().closest('.actions-action').as('sendEmailComponent');
        cy.get('@sendEmailComponent').find('[name="settings[to]"]').type('admin@example.com');
        cy.get('@sendEmailComponent').find('[name="settings[subject]"]').type(strings.sendEmailSubject);
        cy.get('@sendEmailComponent').find('[name="settings[body]"]').type(strings.sendEmailBody, { parseSpecialCharSequences: false });

        cy.screenshot('images/form-add-complete');

        cy.get('[data-cy="submit"]').click();
        cy.screenshot('images/form-browse');
    })

    it('create site page', () => {
        cy.loginAsAdmin();
        cy.visit('/admin/site');
        cy.get('#page-actions a').click();
        cy.get('[name="o:title"]').type('Omeka');
        cy.get('#page-actions button').click();
        cy.visit('/admin/site/s/omeka/add-page')
        cy.get('[name="o:title"]').type('Contact');
        cy.get('#page-actions button').click();
        cy.get('#new-block .option[value="formularium"]').click();
        cy.get('[name="o:block[1][o:data][formularium_form_id]"]').select('1');
        cy.scrollTo('top');
        cy.screenshot('images/site-page');
        cy.get('#page-actions button').click();
    });

    it('use form', () => {
        cy.visit('/s/omeka/page/contact')
        cy.screenshot('images/site-form');

        cy.get('[name="email"]').type('user@example.com');
        cy.get('[name="message"]').type(strings.message);
        cy.get('#content form button').click();
    });

    it('manage submission', () => {
        cy.loginAsAdmin();
        cy.visit('/admin/formularium/form-submission');
        cy.screenshot('images/form-submission-browse');

        cy.get('[data-cy="details"]').click();
        cy.screenshot('images/form-submission-browse-show-details');
    });

    it('show component settings', () => {
        cy.loginAsAdmin();
        for (const componentType of ['checkbox', 'select', 'file_input', 'html', 'recaptcha', 'textarea', 'text_input', 'user_email']) {
            cy.visit('/admin/formularium/form/add');
            cy.get('[data-cy="add-component"]').click();
            cy.get(`#component-selector .option[data-type="${componentType}"]`).click();
            cy.focused().closest('.components-component').find('.formularium-form-component-settings-fieldset')
                .screenshot(`images/form-component/${componentType}`);
        }
    });

    it('show action settings', () => {
        cy.loginAsAdmin();
        for (const actionType of ['email']) {
            cy.visit('/admin/formularium/form/add');
            cy.get('[data-cy="add-action"]').click();
            cy.get(`#action-selector .option[data-type="${actionType}"]`).click();
            cy.focused().closest('.actions-action').find('.formularium-form-action-settings-fieldset')
                .screenshot(`images/form-action/${actionType}`);
        }
    });
})
